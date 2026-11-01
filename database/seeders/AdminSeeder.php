<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@plastomachin.ir')],
            [
                'name' => 'مدیر پلاستو ماشین',
                'password' => env('ADMIN_PASSWORD', 'Admin@1234'),
                'is_admin' => true,
            ],
        );
    }
}
