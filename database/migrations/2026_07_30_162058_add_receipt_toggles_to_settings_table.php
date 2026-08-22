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
            $table->boolean('show_phone_on_receipt')->default(true)->after('shop_phone');
            $table->boolean('show_address_on_receipt')->default(true)->after('show_phone_on_receipt');
            $table->boolean('show_logo_on_receipt')->default(false)->after('show_address_on_receipt');
            $table->string('logo_url')->nullable()->after('show_logo_on_receipt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'show_phone_on_receipt', 
                'show_address_on_receipt', 
                'show_logo_on_receipt', 
                'logo_url'
            ]);
        });
    }
};
