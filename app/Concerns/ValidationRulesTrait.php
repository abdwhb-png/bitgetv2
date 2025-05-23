<?php

namespace App\Concerns;

use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;
use App\Http\Helpers\UtilsHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

trait ValidationRulesTrait
{

    protected function existsInColumns(string $table, array $columns, string $value)
    {
        foreach ($columns as $column) {
            if (DB::table($table)->where($column, $value)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function rolesPermsRules(?string $action = 'store', ?string $section = ''): array
    {
        if ($action == 'update') {
            return [
                'section' => 'required|string|in:roles,permissions',
                'roles' => [Rule::requiredIf($section == 'roles'), 'array', 'min:1', 'max:1'],
                'roles.*' => ['string', 'exists:roles,name'],
                'permissions' => [Rule::requiredIf($section == 'permissions'), 'array', 'min:1'],
                'permissions.*' => ['string', 'exists:permissions,name'],
            ];
        }

        return [];
    }


    public function adminRules(?string $action = 'store', ?string $section = ''): array
    {
        if ($action == 'update') {
            return [
                'section' => 'required|string|in:password',
                'password' => [Rule::requiredIf($section == 'password'), 'string', 'confirmed'],
            ];
        }

        return [];
    }


    public function userRules(?string $action = 'store', ?string $section = ''): array
    {
        if ($action == 'update') {
            return [
                'section' => 'required|string|in:account,balance,wallet',
                'amount' => [Rule::requiredIf($section == 'balance'), 'numeric', 'min:0'],
                'address' => [Rule::requiredIf($section == 'wallet'), 'string', 'max:255'],
                'can_trade' => [Rule::requiredIf($section == 'account'), 'in:0,1'],
                'profit_type' => [Rule::requiredIf($section == 'account'), 'string', 'in:' . implode(',', array_keys(config('vars.profit_types', [])))],
                'profit_min' => [Rule::requiredIf($section == 'account'), 'numeric', 'min:0', 'max:100'],
                'profit_max' => [Rule::requiredIf($section == 'account'), 'numeric', 'min:0', 'max:100'],
            ];
        }

        return [];
    }


    public function verificationRules(?string $action = 'store', ?array $statuses = []): array
    {
        if ($action == 'update') {
            return [
                'type' => 'required|string|in:emails,kycs',
                'status' => 'required|string|in:' . implode(',', $statuses),
            ];
        }

        return [
            'id_type' => 'required|string|in:' . implode(',', config('vars.identification_ids', [])),
            'issued_by' => 'required|string',
            'verification_type' => 'required|string|in:' . implode(',', config('vars.verification_types', [])),
            'photo' => 'required|file|mimes:jpg,jpeg,png,heic|max:' . config('vars.validation.image_max_size', 1024 * 5),
        ];
    }


    public function paymentProofRules(): array
    {
        return [
            'ref' => 'nullable|string|max:255',
            'method' => 'required|string',
            'amount' => 'required|numeric',
            'photo' => 'required|file|mimes:jpg,jpeg,png,heic|max:' . config('vars.validation.image_max_size', 1024 * 5),
        ];
    }



    public function walletAddressRules(): array
    {
        return [
            'id' => 'required|integer',
            'wallet_address' => 'required|string',
        ];
    }


    public function orderRules(?string $action = 'store'): array
    {
        if ($action == 'close') {
            return [
                'price' => 'nullable|numeric',
                'time' => 'nullable|integer',
            ];
        }

        if ($action == 'update') {
        }

        return [
            'type' => 'required|string|in:BUY UP,BUY FALL',
            'symbol' => 'required|string',
            'quote' => 'required|string',
            'base' => 'required|string',
            'asset' => 'required|string|in:USDT,' . implode(',', UtilsHelper::getPMethods('symbols')),
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'expiration' => 'required|integer',
            'time' => 'nullable|integer',
        ];
    }


    public function transactionRules(?string $action = 'store')
    {
        if ($action == 'update') {
            return [
                'status' => 'required|string|in:pending,approved,rejected',
            ];
        }

        return [
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:5',
            'pay_method' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!$this->existsInColumns('payment_methods', ['symbol', 'name'], $value)) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                },
            ],
            'binded_address' => 'nullable|string|max:104'
        ];
    }

    public function swapRules(): array
    {
        return [
            'from' => 'required|array|min:2',
            'to' => 'required|array|min:2',
            'from.wallet' =>
            [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!$this->existsInColumns('payment_methods', ['symbol', 'name'], $value)) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                },
            ],
            'to.wallet' =>
            [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!$this->existsInColumns('payment_methods', ['symbol', 'name'], $value)) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                },
            ],
            'from_amount' => 'required|numeric',
            'to_amount' => 'required|numeric',
        ];
    }


    public function pMethodRules(?string $action = 'store', $id = null)
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // 'in:' . implode(',', array_values(UtilsHelper::getPMethods('names'))),
                Rule::unique('payment_methods', 'name')->ignore($id),
            ],
            'symbol' => [
                'required',
                'string',
                'max:255',
                // 'in:' . implode(',', array_values(UtilsHelper::getPMethods('symbols'))),
                Rule::unique('payment_methods', 'symbol')->ignore($id),
            ],
            'address' => 'required|string|max:255',
        ];
    }


    public function cServiceRules(?string $action = 'store', $id = null)
    {
        if ($action == 'update') {
            if (!$id) {
                throw new \Exception('cService rules requires id');
            }

            return [
                'title' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('customer_services', 'title')->ignore($id),
                ],
                'url' => 'nullable|url:http,https',
            ];
        }

        return [];
    }


    public function settingRules(?string $action = 'store', $item = null)
    {
        if ($action == 'update') {
            return [
                'handling_fee' => 'required|numeric|min:0|max:100',
                'tcs' => 'nullable|string',
                'about_us' => 'nullable|string',
            ];
        }

        return [];
    }
}
