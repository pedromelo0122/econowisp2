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

use App\Helpers\Common\JsonUtils;
use Illuminate\Support\Arr;

trait FakeFields
{
	/**
	 * Refactor the request array to something that can be passed to the model's create or update function.
	 * The resulting array will only include the fields that are stored in the database and their values,
	 * plus the '_token' and 'redirect_after_save' variables.
	 *
	 * @param array $inputArray
	 * @param string $form
	 * @return array
	 */
	public function compactFakeFields(array $inputArray, string $form = 'create')
	{
		if (empty($inputArray)) {
			$inputArray = request()->all();
		}
		
		$fakeFieldColumnsToEncode = [];
		
		// Get the right fields according to the form type (create/update)
		$fields = match (strtolower($form)) {
			'update' => $this->updateFields,
			default  => $this->createFields,
		};
		
		$defaultFakeColumn = 'extras';
		
		// Go through each defined field
		foreach ($fields as $field) {
			$fieldName = $field['name'] ?? null;
			$fieldType = $field['type'] ?? null;
			$isFakeable = (bool)($field['fake'] ?? false);
			$fakeColumn = $field['store_in'] ?? $defaultFakeColumn;
			
			if (empty($fieldName) || empty($fieldType)) {
				continue;
			}
			if ($fieldType == 'custom_html') {
				continue;
			}
			if (!array_key_exists($fieldName, $inputArray)) {
				continue;
			}
			
			// If it's a fake field
			if ($isFakeable) {
				// Add it to the request in its appropriate variable - the one defined, if defined
				if (!empty($fakeColumn)) {
					$inputArray[$fakeColumn][$fieldName] = $inputArray[$fieldName];
					
					// Remove the fake field
					Arr::pull($inputArray, $fieldName);
					
					if (!in_array($fakeColumn, $fakeFieldColumnsToEncode, true)) {
						$fakeFieldColumnsToEncode[] = $fakeColumn;
					}
				}
			}
		}
		
		// json_encode all fake_value columns if applicable in the database, so they can be properly stored and interpreted
		if (is_array($fakeFieldColumnsToEncode) && count($fakeFieldColumnsToEncode) > 0) {
			/**
			 * @var \App\Models\Page $model (for example)
			 * To make some methods clickable in IDE (i.e. PhpStorm), we need a model that uses specific traits.
			 * The Page model uses the CRUD/xPanel traits.
			 * So that needs to be changed in other projects where Page doesn't exist or no longer use these traits.
			 */
			$model = $this->model;
			foreach ($fakeFieldColumnsToEncode as $column) {
				$isTranslatableModel = (
					property_exists($model, 'translatable')
					&& method_exists($model, 'getTranslatableAttributes')
					&& in_array($column, $model->getTranslatableAttributes(), true)
				);
				
				if (!$isTranslatableModel) {
					if ($model->shouldEncodeFake($column)) {
						$inputArray[$column] = JsonUtils::ensureJson($inputArray[$column]);
					}
				}
				
				$inputArray[$column] = JsonUtils::ensureJson($inputArray[$column]);
			}
		}
		
		// If there are no fake fields defined, this will just return the original Request in full
		// since no modifications or additions have been made to $inputArray
		return $inputArray;
	}
}
