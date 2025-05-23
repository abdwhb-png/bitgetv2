<?php

namespace Database\Seeders;

use App\Models\OrderType;
use App\Models\PaymentMethod;
use App\Models\SystemSetting;
use App\Models\CustomerService;
use Illuminate\Database\Seeder;
use App\Http\Helpers\UtilsHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create customer services
        $customerServices = [
            'Telegram Service 1',
            'Customer Service 2',
            'Whatsapp Service 1',
            'Whatsapp Service 2',
        ];

        collect($customerServices)->each(function ($customerService) {
            CustomerService::create([
                'title' => $customerService,
            ]);
        });


        // create system settings
        SystemSetting::create([
            'announcement' => 'Dear users, please note that our workbench operates from 10:00 AM to 23:00 PM.',
            'tcs' => fake()->text(5000),
            'about_us' => fake()->text(1000),
        ]);

        // create payment methods
        collect(UtilsHelper::getPMethods())->each(function ($pMethod) {
            PaymentMethod::create([
                'name' => $pMethod['name'],
                'symbol' => $pMethod['symbol'],
                'address' => $pMethod['symbol'] . '0x' . fake()->sha256()
            ]);
        });


        // create order types
        $orderTypes = [
            'Buy Up',
            'Buy Fall',
            'Sell',
            'Long',
            'Short',
        ];

        collect($orderTypes)->each(function ($orderType) {
            OrderType::create([
                'label' => $orderType,
            ]);
        });
    }
}
