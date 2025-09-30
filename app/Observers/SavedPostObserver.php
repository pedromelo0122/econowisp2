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

use App\Models\SavedPost;
use Throwable;

class SavedPostObserver extends BaseObserver
{
	/**
	 * Listen to the Entry saved event.
	 *
	 * @param SavedPost $savedPost
	 * @return void
	 */
	public function saved(SavedPost $savedPost)
	{
		// Removing Entries from the Cache
		$this->clearCache($savedPost);
	}
	
	/**
	 * Listen to the Entry deleted event.
	 *
	 * @param SavedPost $savedPost
	 * @return void
	 */
	public function deleted(SavedPost $savedPost)
	{
		// Removing Entries from the Cache
		$this->clearCache($savedPost);
	}
	
	/**
	 * Removing the Entity's Entries from the Cache
	 *
	 * @param $savedPost
	 * @return void
	 */
	private function clearCache($savedPost): void
	{
		try {
			cache()->flush();
		} catch (Throwable $e) {
		}
	}
}
