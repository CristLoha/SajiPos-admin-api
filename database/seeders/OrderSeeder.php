<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashier = \App\Models\User::where('roles', 'staff')->first() ?? \App\Models\User::first();
        $products = \App\Models\Product::limit(5)->get();

        if ($products->isEmpty()) {
            return;
        }

        // Buat Order 1: Dine in dengan Pajak & Service Charge
        $order1 = \App\Models\Order::create([
            'cashier_id' => $cashier->id,
            'transaction_time' => now()->subHours(2)->format('Y-m-d H:i:s'),
            'sub_total' => 60000,
            'discount_amount' => 10000, // diskon welcome
            'shipping_cost' => 0,
            'service_charge' => 3000, // 5% service charge
            'tax' => 5300, // 10% tax dari (sub_total - discount + service_charge)
            'total' => 58300,
            'payment_method' => 'qris',
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $products[0]->id,
            'quantity' => 2,
            'price' => 20000,
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $products[1]->id,
            'quantity' => 1,
            'price' => 20000,
        ]);

        // Buat Order 2: Take Away dengan Ongkir
        $order2 = \App\Models\Order::create([
            'cashier_id' => $cashier->id,
            'transaction_time' => now()->subMinutes(30)->format('Y-m-d H:i:s'),
            'sub_total' => 35000,
            'discount_amount' => 0,
            'shipping_cost' => 10000, // ongkir
            'service_charge' => 0,
            'tax' => 3500, // 10% tax
            'total' => 48500,
            'payment_method' => 'cash',
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $products[2]->id,
            'quantity' => 1,
            'price' => 35000,
        ]);
    }
}
