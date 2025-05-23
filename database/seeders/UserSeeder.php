<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(2)->role('admin')->withInfos()->create();
        User::factory()->role('root')->withInfos()->create([
            'email' => 'winnerk088@gmail.com',
        ]);
        User::factory()->count(1)->role('super-admin')->withInfos()->create();
        User::factory()->count(2)->role('user')->withInfos()->create();
    }
}
