<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_time');
            $table->double('sub_total'); // total harga produk sebelum biaya tambahan & diskon
            $table->double('discount_amount')->default(0); // nilai diskon yang dikurangkan
            $table->double('shipping_cost')->default(0); // biaya pengiriman (ongkir)
            $table->double('service_charge')->default(0); // biaya layanan
            $table->double('tax')->default(0); // nominal pajak
            $table->double('total'); // grand total final (sub_total + shipping_cost + service_charge + tax - discount)
            $table->string('payment_method'); // cash, qris, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
