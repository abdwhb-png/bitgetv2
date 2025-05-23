<?php

namespace App\Listeners;

use App\NotifData;
use App\Models\User;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Events\TransactionEvent;
use App\Http\Helpers\UtilsHelper;
use App\Notifications\DefaultNotif;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransactionListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        // Constructor for TransactionListener. Currently empty.
    }

    /**
     * Process a deposit transaction by incrementing the balance.
     *
     * @param Transaction $transaction The transaction object.
     * @param Balance $balance The balance object to update.
     * @return bool True if the deposit was successful, false otherwise.
     */
    protected function makeDeposit(Transaction $transaction, Balance $balance): bool
    {
        // Determine the amount to deposit, converted if available.
        $amount = $transaction->converted_amount ?? $transaction->amount;

        // Increment the balance by the deposit amount.
        $ok = $balance->increment('amount', $amount);

        return $ok;
    }

    /**
     * Process a withdrawal transaction by decrementing the balance.
     *
     * @param Transaction $transaction The transaction object.
     * @param Balance $balance The balance object to update.
     * @return bool True if the withdrawal was successful, false otherwise.
     */
    protected function makeWithdrawal(Transaction $transaction, Balance $balance): bool
    {
        // Determine the amount to withdraw, converted if available.
        $amount = $transaction->converted_amount ?? $transaction->amount;

        if ($amount >= $balance->amount) {
            // If withdrawal amount exceeds balance, set balance to 0.
            $rest = $amount - $balance->amount;

            $ok = $balance->update([
                'amount' => 0,
            ]);
        } else {
            // Otherwise, subtract the withdrawal amount from the balance.
            $ok = $balance->update(['amount' => $balance->amount - $amount]);
        }

        return $ok;
    }

    /**
     * Handle a transaction that has been created.
     *
     * @param Transaction $transaction The transaction object.
     * @param User $user The user associated with the transaction.
     * @param UserAccount $account The user's account.
     * @param Balance $balance The balance object.
     */
    protected function handleCreated(Transaction $transaction, User $user, UserAccount $account, Balance $balance)
    {
        if ($transaction->status == 'pending') {
            // Notify admins about a pending transaction.
            $adminNotifData = new NotifData($user->info->fullName() . ' submited a ' . $transaction->type . ' of <b>' . $transaction->textAmount() . '</b>');
            $adminNotifData->setSubject("New " . $transaction->type . " transaction");
            $adminNotifData->setBody("<b>Method</b>: " . $transaction->method . "<br><b>Binded Address</b>: " . $transaction->binded_address);

            UtilsHelper::notifySuperAdmins($adminNotifData);
        }
    }

    /**
     * Handle a transaction that has been updated.
     *
     * @param Transaction $transaction The transaction object.
     * @param User $user The user associated with the transaction.
     * @param UserAccount $account The user's account.
     * @param Balance $balance The balance object.
     */
    protected function handleUpdated(Transaction $transaction, User $user, UserAccount $account, Balance $balance)
    {
        $title = '';

        // Determine the notification title based on transaction type.
        if ($transaction->type == 'withdrawal' || $transaction->type == 'deposit') {
            $titleText = $transaction->type == 'withdrawal' ? "Account Payout" : "Account Deposit";
            $title = $titleText . " - " . $account->currency . " - TraceID " . $transaction->ref_id;
        }

        $notifData = new NotifData($title);

        $notifData->setSubject("Transaction :" . $transaction->ref_id . " " . $transaction->status);

        if ($transaction->status == 'approved') {
            if ($transaction->type == 'withdrawal') {
                // Process the withdrawal and notify the user.
                $this->makeWithdrawal($transaction, $balance);
                $notifData->setBody("We have successfully processed your withdrawal request for " . $transaction->textAmount() . ".");
            }

            if ($transaction->type == 'deposit') {
                // Process the deposit and notify the user.
                $this->makeDeposit($transaction, $balance);
                $notifData->setBody("We have successfully processed your deposit for " . $transaction->textAmount() . ".");
            }
        }

        if ($transaction->status == 'rejected') {
            // Notify the user of a rejected transaction.
            $notifData->setBody("Your " . $transaction->type . " transaction of " . $transaction->textAmount() . " has been rejected");
        }

        // Send the notification if both title and body are set.
        if ($notifData->getTitle() && $notifData->getBody()) {
            $user->notify(new DefaultNotif($notifData));
        }
    }


    public function handleDeleted($transaction, $state): void
    {
        if ($state == 'created') {
            // Log and notify if the transaction has been deleted.
            $notifData = new NotifData("TransactionListener execution halted.", "Transaction " . $transaction->id . " has been deleted.<br> <b>Reason</b>: " . $transaction->deleted_reason);
            $notifData->setSubject("Transaction deleted");

            UtilsHelper::notifyRoots($notifData);

            Log::channel('transactions')->warning($notifData->getBody());
        }
    }

    /**
     * Handle the event.
     *
     * @param TransactionEvent $event The transaction event object.
     */
    public function handle(TransactionEvent $event): void
    {
        $transaction = $event->transaction;

        if ($transaction->trashed()) {
            $this->handleDeleted($transaction, $event->state);

            return;
        }

        // Skip handling if transaction type is 'swap' or if the balance is not provided.
        if ($transaction->type == 'swap') {
            return;
        }

        // Handle the transaction based on its state.

        if ($event->state == 'created') {
            $this->handleCreated($transaction, $event->user, $event->account, $event->balance);
        }

        if ($event->state == 'updated') {
            $this->handleUpdated($transaction, $event->user, $event->account, $event->balance);
        }
    }
}
