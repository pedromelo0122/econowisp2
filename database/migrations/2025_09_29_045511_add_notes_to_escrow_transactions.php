<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('escrow_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('lc_escrow_transactions', 'notes')) {
                $table->text('notes')->nullable()->after('cancelled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('escrow_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('lc_escrow_transactions', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
