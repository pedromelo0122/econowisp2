<?php
/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: Mayeul Akpovi (BeDigit - https://bedigit.com)
 *
 * LICENSE
 * -------
 * This software is provided under a license agreement and may only be used or copied
 * in accordance with its terms, including the inclusion of the above copyright notice.
 * As this software is sold exclusively on CodeCanyon,
 * please review the full license details here: https://codecanyon.net/licenses/standard
 */

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
        // Evita error "table already exists" en instalaciones donde la tabla ya existe
        if (Schema::hasTable('escrow_transactions')) {
            return;
        }

        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('buyer_id');
            $table->unsignedBigInteger('seller_id');
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3)->nullable();
            $table->string('status', 50);
            $table->timestamp('hold_until')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['post_id']);
            $table->index(['buyer_id']);
            $table->index(['seller_id']);
            $table->index(['status']);
            $table->index(['currency_code']);

            // OJO: no poner prefijo aquí; Laravel aplicará DB_TABLES_PREFIX (p.ej. lc_) automáticamente
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
            $table->foreign('buyer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('seller_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('escrow_transactions')) {
            Schema::drop('escrow_transactions');
        }
    }
};
