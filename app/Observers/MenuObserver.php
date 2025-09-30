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

namespace App\Observers;

use App\Models\Menu;
use Throwable;

class MenuObserver extends BaseObserver
{
	/**
	 * Listen to the Entry saved event.
	 *
	 * @param Menu $menu
	 * @return void
	 */
	public function saved(Menu $menu)
	{
		// Removing Entries from the Cache
		$this->clearCache($menu);
	}
	
	/**
	 * Listen to the Entry deleted event.
	 *
	 * @param Menu $menu
	 * @return void
	 */
	public function deleted(Menu $menu)
	{
		// Removing Entries from the Cache
		$this->clearCache($menu);
	}
	
	/**
	 * Removing the Entity's Entries from the Cache
	 *
	 * @param $menu
	 * @return void
	 */
	private function clearCache($menu): void
	{
		try {
			cache()->forget("menu.{$menu->location}");
			cache()->flush();
		} catch (Throwable $e) {
		}
	}
}
