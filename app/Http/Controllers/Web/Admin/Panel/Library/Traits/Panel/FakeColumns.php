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

namespace App\Http\Controllers\Web\Admin\Panel\Library\Traits\Panel;

trait FakeColumns
{
	/**
	 * Returns an array of database columns names, that are used to store fake values.
	 * Returns ['extras'] if no columns have been found.
	 *
	 * @param string $form
	 * @return array
	 */
	public function getFakeColumnsAsArray(string $form = 'create'): array
	{
		$fakeFieldColumnsToEncode = [];
		
		// Get the right fields according to the form type (create/update)
		$fields = match (strtolower($form)) {
			'update' => $this->updateFields,
			default  => $this->createFields,
		};
		
		$defaultFakeColumn = 'extras';
		
		foreach ($fields as $field) {
			$isFakeable = (bool)($field['fake'] ?? false);
			$fakeColumn = $field['store_in'] ?? $defaultFakeColumn;
			
			// If it's a fake field
			if ($isFakeable) {
				// Add it to the request in its appropriate variable - the one defined, if defined
				if (!empty($fakeColumn)) {
					if (!in_array($fakeColumn, $fakeFieldColumnsToEncode, true)) {
						$fakeFieldColumnsToEncode[] = $fakeColumn;
					}
				}
			}
		}
		
		if (!count($fakeFieldColumnsToEncode)) {
			return [$defaultFakeColumn];
		}
		
		return $fakeFieldColumnsToEncode;
	}
}
