<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop old status column
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }

            // Make roles nullable
            $table->enum('roles', ['admin', 'staff', 'user'])->nullable()->change();

            // Add new columns
            $table->enum('status_akun', ['pending_approval', 'approved', 'rejected', 'nonaktif'])
                  ->default('approved')
                  ->after('roles');
                  
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status_akun', 'approved_by', 'approved_at', 'rejection_reason']);
            
            // Re-add old status
            $table->enum('status', ['pending', 'active', 'rejected'])->default('active');
            
            // Note: making roles non-nullable again might fail if there are nulls, 
            // so we skip changing it back to non-nullable or set default.
            // But this is just down migration.
        });
    }
};
