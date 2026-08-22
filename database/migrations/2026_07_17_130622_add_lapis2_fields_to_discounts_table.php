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
        Schema::table('discounts', function (Blueprint $table) {
            $table->decimal('max_discount', 15, 2)->nullable()->after('value');
            $table->decimal('min_transaction', 15, 2)->nullable()->after('max_discount');
            $table->date('start_date')->nullable()->after('status');
            $table->integer('quota')->nullable()->after('expired_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn(['max_discount', 'min_transaction', 'start_date', 'quota']);
        });
    }
};
