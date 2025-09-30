<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('escrow_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('escrow_transactions', 'buyer_id')) {
                $table->unsignedBigInteger('buyer_id')->after('post_id');
                $table->index('buyer_id');
                $table->foreign('buyer_id')
                      ->references('id')
                      ->on('users')
                      ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('escrow_transactions', 'seller_id')) {
                $table->unsignedBigInteger('seller_id')->after('buyer_id');
                $table->index('seller_id');
                $table->foreign('seller_id')
                      ->references('id')
                      ->on('users')
                      ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('escrow_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('escrow_transactions', 'buyer_id')) {
                $table->dropForeign(['buyer_id']);
                $table->dropIndex(['buyer_id']);
                $table->dropColumn('buyer_id');
            }

            if (Schema::hasColumn('escrow_transactions', 'seller_id')) {
                $table->dropForeign(['seller_id']);
                $table->dropIndex(['seller_id']);
                $table->dropColumn('seller_id');
            }
        });
    }
};
