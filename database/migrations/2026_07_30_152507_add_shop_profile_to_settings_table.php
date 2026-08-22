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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('shop_name')->nullable()->after('id');
            $table->text('shop_address')->nullable()->after('shop_name');
            $table->string('shop_phone')->nullable()->after('shop_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['shop_name', 'shop_address', 'shop_phone']);
        });
    }
};
