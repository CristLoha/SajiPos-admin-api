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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->onDelete('set null')->after('sub_total');
            $table->string('discount_name')->nullable()->after('discount_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
            $table->dropColumn(['discount_id', 'discount_name']);
        });
    }
};
