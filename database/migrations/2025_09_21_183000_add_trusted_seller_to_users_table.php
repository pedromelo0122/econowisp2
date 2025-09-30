<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'trusted_seller')) {
                $table->boolean('trusted_seller')->default(false)->index();
            }
            if (!Schema::hasColumn('users', 'trusted_at')) {
                $table->timestamp('trusted_at')->nullable()->index();
            }
            if (!Schema::hasColumn('users', 'trusted_by')) {
                $table->unsignedBigInteger('trusted_by')->nullable()->index();
                $table->foreign('trusted_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'trusted_by')) {
                $table->dropForeign(['trusted_by']);
            }
            $table->dropColumn(['trusted_seller','trusted_at','trusted_by']);
        });
    }
};
