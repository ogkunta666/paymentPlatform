<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Kunta user
        $kunta = User::factory()->create([
            'name' => 'Kunta',
            'email' => 'kunta@example.com',
            'password' => Hash::make('Super_Secret_Pw2025!'),
        ]);

        // Create orders and payments for Kunta
        Order::factory(3)->create([
            'user_id' => $kunta->id,
        ])->each(function ($order) {
            Payment::factory(rand(1, 2))->create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
            ]);
        });

        // Create 10 fake users with orders and payments
        User::factory(10)->create()->each(function ($user) {
            Order::factory(rand(1, 5))->create([
                'user_id' => $user->id,
            ])->each(function ($order) {
                Payment::factory(rand(1, 3))->create([
                    'order_id' => $order->id,
                    'amount' => $order->total_amount,
                ]);
            });
        });
    }
}
