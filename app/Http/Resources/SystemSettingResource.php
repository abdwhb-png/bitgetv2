<?php

namespace App\Http\Resources;

use App\Models\OrderType;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemSettingResource extends JsonResource
{


    protected function getVerificationStatuses(): array
    {
        $verficationsStatuses = [];

        foreach (config('vars.verification_types', []) as $type) {
            $statuses = config('vars.' . $type . '_statuses', []);
            $verficationsStatuses[$type] = collect(config('vars.statuses'))->filter(function ($status) use ($statuses) {
                return in_array($status['label'], $statuses);
            })->toArray();
        }

        return $verficationsStatuses;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pMethods = PaymentMethod::where('status', 1)->groupBy('symbol')->get()->map(function ($method) {
            return [
                'id' => $method->id,
                'symbol' => $method->symbol,
                'name' => $method->symbol == 'USDT' ? 'Tether' : $method->name,
                'address' => $method->address,
            ];
        });

        return array_merge(parent::toArray($request), [
            'appEnv' => config('app.env'),
            'appName' => env('APP_NAME'),
            'appTexts' => config('texts'),
            'appLogo' => asset('/app_assets/images/logo/144.png'),

            'countries' => Storage::json('countries.json'),

            'paymentMethods' => PaymentMethod::where('status', 1)->get(),
            'goodSymbols' => config('vars.good_symbols'),

            'dataTypes' => [
                'order' => OrderType::where('status', 1)->get()->pluck('label')->map(function ($label) {
                    return strtoupper($label);
                }),
                'transaction' => config('vars.transaction_types'),
                'profit' => config('vars.profit_types'),
                'verification' => config('vars.verification_types'),
            ],

            'statuses' => [
                'all' => config('vars.statuses'),
                'order' => config('vars.order_statuses'),
                'transaction' => config('vars.transaction_statuses'),
                'verification' => $this->getVerificationStatuses(),
            ],

            'globalFilterFields' => [
                'user' => [
                    'detail.email',
                    'detail.first_name',
                    'detail.last_name',
                    'full_name',
                ],
                'transaction' => [
                    "detail.user",
                    "detail.amount",
                    "detail.converted_amount",
                    "detail.method",
                    "detail.binded_address",
                    "detail.ref_id",
                    "dates.created_at",
                    "dates.updated_at",
                    "dates.created",
                    "dates.updated",
                ],
                'order' => [
                    "detail.base.user",
                    "detail.base.symbol",
                    "detail.base.duration",
                    "detail.more.ref_id",
                    "dates.opened_at",
                    "dates.closed_at",
                    "dates.created_at",
                    "dates.updated_at",
                ],
                'verification' => [
                    'kyc' => [
                        'detail.user',
                        'detail.id_type',
                        'detail.id_issued_by',
                        'detail.created_at',
                    ],
                    'email' => [
                        'detail.user',
                        'detail.email',
                        'detail.email_verified_at',
                    ],
                ]
            ],

            'pusher' => [
                'notification' => [
                    'event' => 'Illuminate\\Notifications\\Events\\BroadcastNotificationCreated',
                    'channel' => 'private-App.Models.User'
                ],
                'user_info' => [
                    'event' => 'UserInfoEvent',
                    'channel' => 'private-App.Models.User.Info'
                ],
                'user_account' => [
                    'event' => 'UserAccountEvent',
                    'channel' => 'private-App.Models.User.Account'
                ],
                'transaction' => [
                    'event' => 'TransactionEvent',
                    'channel' => 'private-App.Models.Transaction'
                ],
                'order' => [
                    'event' => 'OrderEvent',
                    'channel' => 'private-App.Models.Order'
                ],
                'all_transactions' => [
                    'event' => 'TransactionEvent',
                    'channel' => 'private-transactions'
                ],
                'all_orders' => [
                    'event' => 'OrderEvent',
                    'channel' => 'private-orders'
                ],
                'all_users' => [
                    'event' => 'UserEvent',
                    'channel' => 'private-users'
                ],
            ],

            'default_coin_img' => asset('/app_assets/images/coin/default_.png'),
            'default_avatar_url' => asset('/app_assets/images/avt/avt27.jpg'),
        ]);
    }
}
