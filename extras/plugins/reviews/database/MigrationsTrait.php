<?php

namespace extras\plugins\reviews\database;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait MigrationsTrait
{
	/**
	 * @return void
	 */
	private static function migrationsInstall(): void
	{
		Schema::disableForeignKeyConstraints();
		
		// Create the 'reviews' table, by dropping it if exists
		Schema::dropIfExists('reviews');
		Schema::create('reviews', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('post_id')->unsigned();
			$table->bigInteger('user_id')->unsigned()->nullable();
			$table->integer('rating');
			$table->text('comment');
			$table->tinyInteger('approved')->unsigned()->default(1);
			$table->tinyInteger('spam')->unsigned()->default(0);
			$table->timestamps();
			
			$table->index('post_id');
			$table->index('user_id');
		});
		
		// Add the 'rating_cache' column in the 'posts' table
		if (!Schema::hasColumn('posts', 'rating_cache')) {
			Schema::table('posts', function (Blueprint $table) {
				$table->float('rating_cache', 2)
					->unsigned()
					->default(0.0)
					->after('visits');
			});
		}
		
		// Add the 'rating_count' column in the 'posts' table
		if (!Schema::hasColumn('posts', 'rating_count')) {
			Schema::table('posts', function (Blueprint $table) {
				$table->integer('rating_count')
					->unsigned()
					->default(0)
					->after('rating_cache');
			});
		}
		
		Schema::enableForeignKeyConstraints();
	}
	
	/**
	 * @return void
	 */
	private static function migrationsUninstall(): void
	{
		Schema::disableForeignKeyConstraints();
		
		// Drop the 'rating_cache' column from the 'posts' table
		if (Schema::hasColumn('posts', 'rating_cache')) {
			Schema::table('posts', function (Blueprint $table) {
				$table->dropColumn('rating_cache');
			});
		}
		
		// Drop the 'rating_count' column from the 'posts' table
		if (Schema::hasColumn('posts', 'rating_count')) {
			Schema::table('posts', function (Blueprint $table) {
				$table->dropColumn('rating_count');
			});
		}
		
		// Drop the 'reviews' table if exists
		Schema::dropIfExists('reviews');
		
		Schema::enableForeignKeyConstraints();
	}
}
