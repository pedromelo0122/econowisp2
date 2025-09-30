<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('escrow_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('escrow_transactions', 'post_id')) {
                $table->unsignedBigInteger('post_id')->after('reference');

                // índice y relación con posts
                $table->index('post_id');
                $table->foreign('post_id')
                      ->references('id')
                      ->on('posts')
                      ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('escrow_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('escrow_transactions', 'post_id')) {
                $table->dropForeign(['post_id']);
                $table->dropIndex(['post_id']);
                $table->dropColumn('post_id');
            }
        });
    }
};
