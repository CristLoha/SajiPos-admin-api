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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->boolean('include_shipping_in_tax')->default(false);
            $table->decimal('service_fee', 15, 2)->default(0);
            $table->boolean('include_service_fee_in_tax')->default(false);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
