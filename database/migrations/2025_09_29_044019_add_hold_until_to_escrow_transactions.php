<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('escrow_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('escrow_transactions', 'hold_until')) {
                $table->timestamp('hold_until')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('escrow_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('escrow_transactions', 'hold_until')) {
                $table->dropColumn('hold_until');
            }
        });
    }
};
