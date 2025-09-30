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

use App\Helpers\Common\Arr;
use App\Helpers\Common\RepeaterFieldHandler;

trait Update
{
	/*
	|--------------------------------------------------------------------------
	|                                   UPDATE
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * Update a row in the database.
	 *
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	public function update($id, $data)
	{
		// Get the entry/item
		$item = $this->model->query()->findOrFail($id);
		
		// Update fake fields values in array
		$valuesToStore = $this->compactFakeFields($data, 'update');
		
		// Keep only fillable columns
		// Note: Make sure that full form fields are sent to pivot update (i.e. to the syncPivot() method)
		$fillable = $this->model->getFillable();
		$valuesToStore = Arr::only($valuesToStore, $fillable);
		
		// Update the entry in DB
		// $updated = $item->update($valuesToStore);
		foreach ($valuesToStore as $field => $value) {
			$item->{$field} = $value;
		}
		if ($item->isDirty()) {
			$item->save();
		}
		
		// Sync. pivot (if enabled)
		if ($this->isEnabledSyncPivot()) {
			$this->syncPivot($item, $data, 'update');
		}
		
		return $item;
	}
	
	/**
	 * Get all fields needed for the EDIT ENTRY form.
	 *
	 * @param $id
	 * @return array  The fields with attributes, fake attributes and values.
	 */
	public function getUpdateFields($id): array
	{
		$fields = (array)$this->updateFields;
		$entry = $this->getEntry($id);
		
		foreach ($fields as $key => $field) {
			$fieldValue = $field['value'] ?? null;
			
			// Skip if value is already set from field definition
			if (!is_null($fieldValue)) {
				continue;
			}
			
			// Check types of field
			$isFakeable = (bool)($field['fake'] ?? false);
			$fakeColumn = $field['store_in'] ?? null;
			$isFakeable = ($isFakeable && !empty($fakeColumn));
			
			$subFields = $field['subfields'] ?? [];
			$hasSubFields = (!empty($subFields) && is_array($subFields));
			
			// Process field based on whether it has "fake column" or subfields
			if ($isFakeable) {
				$field['value'] = $this->processSimpleFieldWithFakeColumn($field, $entry, $key);
			} else {
				if ($hasSubFields) {
					$field['value'] = $this->processFieldWithSubfields($field, $entry);
				} else {
					$field['value'] = $this->processSimpleField($field, $entry, $key);
				}
			}
			
			$fields[$key] = $field;
		}
		
		// Add required system fields
		$this->addSystemFields($fields, $entry);
		
		return $fields;
	}
	
	// PRIVATE
	
	/**
	 * Process simple field (no subfields)
	 *
	 * @param array $field
	 * @param $entry
	 * @return null
	 */
	private function processSimpleField(array $field, $entry)
	{
		$columnName = $field['name'] ?? null;
		if (empty($columnName)) {
			return null;
		}
		
		return $entry?->{$columnName};
	}
	
	/**
	 * Process simple field with fake column (no subfields)
	 * Handle fake columns (e.g. attributes fakely created for 'field_values')
	 *
	 * @param array $field
	 * @param $entry
	 * @param $key
	 * @return mixed|null
	 */
	private function processSimpleFieldWithFakeColumn(array $field, $entry, $key)
	{
		$fakeColumnName = $field['store_in'] ?? null;
		if (empty($fakeColumnName)) {
			return null;
		}
		
		$columnValue = $entry->{$fakeColumnName} ?? [];
		
		return $columnValue[$key] ?? null;
	}
	
	/**
	 * Process fields that have subfields (repeatable or grouped fields)
	 *
	 * @param array $field
	 * @param $entry
	 * @return array
	 */
	private function processFieldWithSubfields(array $field, $entry)
	{
		$fieldName = $field['name'] ?? null;
		$fieldType = $field['type'] ?? null;
		$subFields = $field['subfields'] ?? [];
		
		if ($fieldType === 'repeatable') {
			return $this->processRepeatableField($fieldName, $subFields, $entry);
		}
		
		return $this->processGroupedField($subFields, $entry);
	}
	
	/**
	 * Process repeatable field type
	 *
	 * @param string $fieldName
	 * @param array $subFields
	 * @param $entry
	 * @return array
	 */
	private function processRepeatableField(string $fieldName, array $subFields, $entry)
	{
		$columnValue = $entry->{$fieldName} ?? [];
		$subFieldsKeys = collect($subFields)->pluck('name')->filter()->toArray();
		
		if (empty($subFieldsKeys)) {
			return [];
		}
		
		// Handle simple repeatable fields (1-2 subfields)
		if (count($subFieldsKeys) <= 2) {
			if (empty($columnValue) || !is_array($columnValue)) {
				return $columnValue;
			}
			
			$repeaterHandler = new RepeaterFieldHandler();
			
			if (count($subFieldsKeys) === 1) {
				$subFieldKey = $subFieldsKeys[0];
				
				return $repeaterHandler->prepareForRepeater(data: $columnValue, singleKey: $subFieldKey);
			}
			
			return $repeaterHandler->prepareForRepeater(data: $columnValue, doubleKeys: $subFieldsKeys);
		}
		
		// Handle complex repeatable fields (3+ subfields)
		$processedValue = [];
		if (is_array($columnValue)) {
			foreach ($subFields as $k => $subField) {
				$subFieldName = $subField['name'] ?? null;
				if ($subFieldName) {
					$processedValue[$k][$subFieldName] = $columnValue[$k][$subFieldName] ?? null;
				}
			}
		}
		
		return $processedValue;
	}
	
	/**
	 * Process grouped field (non-repeatable with subfields)
	 *
	 * @param array $subFields
	 * @param $entry
	 * @return array
	 */
	private function processGroupedField(array $subFields, $entry): array
	{
		$groupedValue = [];
		
		foreach ($subFields as $subField) {
			$subFieldName = $subField['name'] ?? null;
			if (empty($subFieldName)) {
				continue;
			}
			
			$subFieldColumnValue = $entry->{$subFieldName} ?? null;
			if (!is_null($subFieldColumnValue)) {
				$groupedValue[] = $subFieldColumnValue;
			}
		}
		
		return $groupedValue;
	}
	
	/**
	 * Add required system fields (ID and locale)
	 *
	 * @param array $fields
	 * @param $entry
	 * @return void
	 */
	private function addSystemFields(array &$fields, $entry): void
	{
		// Always add hidden input for entry ID
		$fields['id'] = [
			'name'  => $entry->getKeyName(),
			'type'  => 'hidden',
			'value' => $entry->getKey(),
		];
		
		// Add locale field if translations are enabled
		if ($this->model->translationEnabled()) {
			$fields['locale'] = [
				'name'  => 'locale',
				'type'  => 'hidden',
				'value' => request()->input('locale', app()->getLocale()),
			];
		}
	}
}
