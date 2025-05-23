<?php

namespace App\Listeners;

use App\Events\FatalErrorEvent;
use App\NotifData;
use App\Events\OrderEvent;
use App\Http\Helpers\UtilsHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use BinanceApi\Exception\BinanceException;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderListener
{
    protected function closeTradeComplicated($order, $binance)
    {
        try {
            $result = $binance->getTickerPrice(['symbol' => $order->symbol]);
            $price = $result['price'];
            $timestamp = time() * 1000;

            $profit = 0;

            if ($order->type == 'BUY UP') {
                $profit = $price - $order->open_price;
            }

            if ($order->type == 'BUY FALL') {
                $profit = $order->open_price - $price;
            }

            $profit = $profit * $order->quantity;

            $order->update([
                'profit' => $profit,
                'close_price' => $price,
                'close_time' => $timestamp,
                // 'closed_at' => $timestamp,
            ]);

            $balance = $order->account->balances()->where('wallet', $order->asset)->lockForUpdate()->first();

            $rest = $balance->amount + $profit;
            $convProfit = $rest * $price;

            $balance->update([
                'amount' => $convProfit <= 0 ? 0 : $convProfit,
            ]);
        } catch (BinanceException $e) {
            Log::error('Order ' . $order->id . ' updated closed', $e->getMessage());
        } catch (\Throwable $th) {
            event(new FatalErrorEvent($th->getMessage(), $th));
        }
    }


    protected function getProfit($order): array
    {
        $percentage = $order->percentage;
        try {
            $percentage = $order->percentage ?? random_int($order->account->profit_min, $order->account->profit_max) / 100;
        } catch (\Throwable $th) {
            $percentage = random_int(20, 30) / 100;
            Log::channel('orders')->error('Error while getting profit for order ' . $order->id . ' - ' . $th->getMessage());
        } finally {
            $profit = 0;
            $profitType = $order->profit_type ?? $order->account->profit_type;

            if ($profitType !== 'break-even') {
                $profit = (float) $order->quantity * (float) $percentage;
                $profit = $profitType == 'negative' ? $profit * -1 : $profit;
            }

            return [
                'percentage' => $percentage,
                'profit' => (float) $profit,
            ];
        }
    }


    protected function getPrice($order, $binance)
    {
        $price = null;

        try {
            $result = $binance->getTickerPrice(['symbol' => $order->symbol]);
            $price = $result ? $result['price'] : null;
        } catch (BinanceException $e) {
            Log::channel('orders')->error('Order ' . $order->id . ' updated closed' . ' - ' . $e);
        } catch (\Throwable $th) {
            $error = 'Warning: error while closing order : <b>' . $order->id . '</b>';
            event(new FatalErrorEvent($error, $th));
        } finally {
            return $price;
        }
    }


    protected function notifyAdmins($state, $order, $profit = null): void
    {
        if ($state == 'created') {
            $notifData = new NotifData('New order: <span style="text-decoration: underline;">' . $order->ref_id . '</span>');
            $notifData->setBody($order->account->user->info->fullName() . ' have opened an order.');
        }

        if ($state == 'closed') {
            $notifData = new NotifData('Closed order: <span style="text-decoration: underline;">' . $order->ref_id . '</span>');
            $notifData->setBody('Order from ' . $order->account->user->info->fullName() . ' has been closed.<br><b>Profit: </b>' . $profit . ' ' . $order->account->currency);
        }

        if (isset($notifData)) {
            $notifData->setSubject('Message about order ' . $state);
            UtilsHelper::notifySuperAdmins($notifData);
        }
    }


    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }



    /**
     * Handle the event.
     */
    public function handle(OrderEvent $event): void
    {
        $order = $event->order;
        $price = $this->getPrice($order, $event->binance);

        if ($event->state == 'created') {

            $order->update([
                'open_price' => $price ?? $order->open_price,
                'profit_type' => $order->account->profit_type,
                'percentage' => random_int($order->account->profit_min, $order->account->profit_max) / 100,
                'balance_on_create' => $event->balances->sum('amount'),
            ]);

            // send notification to admins
            $this->notifyAdmins('created', $order);
        }


        if ($event->state == 'closed') {
            if ($order->getStatus() == 'closed') {
                $profitResult = $this->getProfit($order);
                $profit = $profitResult['profit'];

                $timestamp = time() * 1000;

                $order->update([
                    'profit' => $profit,
                    'close_price' => $price ?? $order->close_price,
                    'close_time' => $timestamp,
                ]);

                foreach ($event->balances as $balance) {
                    // Calculer le montant restant en ajoutant le profit au montant actuel
                    $remainingBalance = $balance->amount + $profit;

                    // Vérifier si le montant restant est inférieur ou égal à zéro
                    if ($remainingBalance <= 0) {
                        // Mettre à jour le solde à zéro si le montant restant est négatif ou nul
                        $balance->update(['amount' => 0]);

                        // Si le montant restant est exactement zéro, sortir de la boucle
                        if ($remainingBalance == 0) {
                            break; // Sortir de la boucle car le profit est épuisé
                        } else {
                            // Sinon, mettre à jour le profit avec le montant restant
                            $profit = $remainingBalance; // Cela peut devenir négatif
                        }
                    } else {
                        // Si le montant restant est positif, incrémenter le solde avec le profit
                        $balance->update(['amount' => $remainingBalance]);

                        // Sortir de la boucle car on a ajouté le profit
                        break; // Pas besoin de continuer après un ajout réussi
                    }
                }


                // send notification to admins
                $this->notifyAdmins('closed', $order, $profit);
            }
        }
    }
}