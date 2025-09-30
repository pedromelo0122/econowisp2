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

namespace App\Models\Section\Traits;

trait HasSpacing
{
	/**
	 * Generate form fields for Bootstrap spacing configuration
	 *
	 * Creates repeatable form fields for configuring Bootstrap spacing properties
	 * with side, breakpoint, and size options. Supports margin, padding, and gap properties.
	 *
	 * @param array $existingFields Current array of form fields
	 * @param string|null $targetProperty Specific property to generate fields for (e.g., 'm', 'p', 'g')
	 * @param string $fieldSeparator Position of field separators ('start', 'end', 'both')
	 * @param bool $preventHeaderOverlap
	 * @return array Enhanced array of form fields with spacing configuration options
	 */
	protected static function appendSpacingFormFields(
		array   $existingFields = [],
		?string $targetProperty = 'm',
		string  $fieldSeparator = 'start',
		bool    $preventHeaderOverlap = true
	): array
	{
		$spacingProperties = self::getFormattedSpacingProperties($targetProperty);
		$responsiveBreakpoints = self::getFormattedBreakpointOptions();
		$spacingSizes = self::getFormattedSizeOptions();
		
		if (empty($spacingProperties)) return $existingFields;
		
		$generatedFields = [];
		
		if ($fieldSeparator == 'start' || $fieldSeparator == 'both') {
			$generatedFields[] = self::createFormSeparatorField('start');
		}
		
		$breakpointCount = count($responsiveBreakpoints);
		foreach ($spacingProperties as $spacingProperty) {
			$repeatableFieldConfig = [];
			
			$propertyName = $spacingProperty['name'];
			$propertyLabel = $spacingProperty['label'];
			$propertySides = $spacingProperty['formattedSides'];
			$sideCount = count($propertySides);
			
			$repeatableFieldConfig['name'] = $propertyName;
			$repeatableFieldConfig['label'] = $propertyLabel;
			$repeatableFieldConfig['type'] = 'repeatable';
			
			$subFieldConfigs = [];
			
			// Side selection field
			$sideOptions = collect($propertySides)->prepend(trans('admin.select'), '')->toArray();
			$sideSubField = [];
			$sideSubField['name'] = 'side';
			$sideSubField['label'] = "{$propertyLabel} Side";
			$sideSubField['type'] = 'select_from_array';
			$sideSubField['options'] = $sideOptions;
			$sideSubField['allows_null'] = false;
			$sideSubField['wrapper'] = ['class' => 'col-md-4'];
			$subFieldConfigs[] = $sideSubField;
			
			// Breakpoint selection field
			$breakpointOptions = collect($responsiveBreakpoints)->prepend(trans('admin.select'), '')->toArray();
			$breakpointSubField = [];
			$breakpointSubField['name'] = 'breakpoint';
			$breakpointSubField['label'] = 'Breakpoint';
			$breakpointSubField['type'] = 'select_from_array';
			$breakpointSubField['options'] = $breakpointOptions;
			$breakpointSubField['allows_null'] = false;
			$breakpointSubField['wrapper'] = ['class' => 'col-md-4'];
			$subFieldConfigs[] = $breakpointSubField;
			
			// Size selection field
			$sizeOptions = collect($spacingSizes)->prepend(trans('admin.select'), '')->toArray();
			$sizeSubField = [];
			$sizeSubField['name'] = 'size';
			$sizeSubField['label'] = 'Size';
			$sizeSubField['type'] = 'select_from_array';
			$sizeSubField['options'] = $sizeOptions;
			$sizeSubField['allows_null'] = false;
			$sizeSubField['wrapper'] = ['class' => 'col-md-4'];
			$subFieldConfigs[] = $sizeSubField;
			
			$repeatableFieldConfig['subfields'] = $subFieldConfigs;
			
			// Calculate maximum possible combinations
			$maxCombinations = $sideCount * $breakpointCount;
			
			$repeatableFieldConfig['init_rows'] = 1;
			$repeatableFieldConfig['min_rows'] = 0;
			$repeatableFieldConfig['max_rows'] = $maxCombinations;
			$repeatableFieldConfig['reorder'] = false;
			$repeatableFieldConfig['hint'] = trans('admin.bs_spacing_hint');
			$repeatableFieldConfig['wrapper'] = ['class' => 'col-md-12'];
			
			$generatedFields[] = $repeatableFieldConfig;
		}
		
		if ($targetProperty == 'm' && $preventHeaderOverlap) {
			$generatedFields[] = self::createFormPreventHeaderOverlapField();
		}
		
		if ($fieldSeparator == 'end' || $fieldSeparator == 'both') {
			$generatedFields[] = self::createFormSeparatorField('end');
		}
		
		return array_merge($existingFields, $generatedFields);
	}
	
	/**
	 * Build valid Bootstrap spacing CSS classes from configuration array
	 *
	 * Validates and converts spacing configuration into proper Bootstrap CSS classes
	 * following the format: {property}{side}-{breakpoint}-{size}.
	 *
	 * @param array $spacingConfig Array of spacing configurations with side, breakpoint, and size
	 * @param string $targetProperty Bootstrap property to validate against (m, p, g)
	 * @return array Array of valid Bootstrap CSS classes
	 */
	protected static function buildSpacingClasses(array $spacingConfig, string $targetProperty = 'm'): array
	{
		$spacingProperties = self::getFormattedSpacingProperties($targetProperty);
		$currentProperty = current($spacingProperties);
		$validSideVariations = $currentProperty['formattedSides'] ?? [];
		
		$validSides = !empty($validSideVariations) ? array_keys($validSideVariations) : [];
		$validBreakpoints = array_keys(self::getFormattedBreakpointOptions());
		$validSizes = array_keys(self::getFormattedSizeOptions());
		
		$cssClasses = [];
		
		foreach ($spacingConfig as $spacingRule) {
			$side = $spacingRule['side'] ?? '';
			$breakpoint = $spacingRule['breakpoint'] ?? '';
			$size = $spacingRule['size'] ?? '';
			
			// Validate side (should be m, mt, mb, ml, mr, mx, my)
			if (!in_array($side, $validSides)) {
				continue;
			}
			
			// Validate breakpoint (should be sm, md, lg, xl, xxl or empty/blank)
			if (!empty($breakpoint) && !in_array($breakpoint, $validBreakpoints)) {
				continue;
			}
			
			// Validate size (should be 0-5 or auto)
			if (!in_array($size, $validSizes)) {
				continue;
			}
			
			// Build the Bootstrap class following: {property}{side}-{breakpoint}-{size}
			if (!empty($breakpoint)) {
				$bootstrapClass = $side . '-' . $breakpoint . '-' . $size;
			} else {
				$bootstrapClass = $side . '-' . $size;
			}
			
			$cssClasses[] = $bootstrapClass;
		}
		
		return $cssClasses;
	}
	
	/**
	 * Get default margin configuration
	 *
	 * Provides a sensible default margin configuration for Bootstrap spacing.
	 *
	 * @return array Default margin configuration for Bootstrap CSS class mb-4
	 */
	protected static function getDefaultMarginConfiguration(): array
	{
		return [
			[
				'side'       => 'mb',
				'breakpoint' => null,
				'size'       => '4',
			],
		];
	}
	
	// PRIVATE
	
	/**
	 * Retrieve and format Bootstrap spacing properties with side variations
	 *
	 * Builds a comprehensive list of spacing properties (margin, padding, gap) with
	 * their corresponding side variations (top, bottom, left, right, x-axis, y-axis).
	 *
	 * @param string|null $targetPropertyKey Optional filter to return only a specific property
	 * @return array Formatted properties with name, label, and available side variations
	 */
	private static function getFormattedSpacingProperties(?string $targetPropertyKey = 'm'): array
	{
		$spacingConfiguration = getCachedReferrerList('bootstrap-spacing');
		
		$availableProperties = $spacingConfiguration['properties'] ?? [];
		$availableSideVariations = $spacingConfiguration['sides'] ?? [];
		
		if (empty($availableProperties)) {
			return [];
		}
		
		// Filter to specific property if requested
		$propertiesToProcess = (
			$targetPropertyKey !== null
			&& array_key_exists($targetPropertyKey, $availableProperties)
		)
			? [$targetPropertyKey => $availableProperties[$targetPropertyKey]]
			: $availableProperties;
		
		$formattedProperties = [];
		
		foreach ($propertiesToProcess as $propertyKey => $propertyConfig) {
			$formattedProperties[$propertyKey] = [
				'name'           => $propertyConfig['name'],
				'label'          => $propertyConfig['label'],
				'formattedSides' => self::buildPropertySideVariations(
					$propertyKey,
					$propertyConfig['label'],
					$availableSideVariations
				),
			];
		}
		
		return $formattedProperties;
	}
	
	/**
	 * Build formatted side variations for a Bootstrap spacing property
	 *
	 * Combines property keys with side variations to create complete spacing class prefixes
	 * (e.g., 'm' + 't' = 'mt' for margin-top).
	 *
	 * @param string $propertyKey Base Bootstrap property key (m, p, g)
	 * @param string $propertyLabel Human-readable property name
	 * @param array $availableSides Available side variations with keys and labels
	 * @return array Combined property-side keys with descriptive labels
	 */
	private static function buildPropertySideVariations(
		string $propertyKey,
		string $propertyLabel,
		array  $availableSides
	): array
	{
		$formattedSideVariations = [];
		
		foreach ($availableSides as $sideKey => $sideLabel) {
			$combinedPropertyKey = ($sideKey === 'blank') ? $propertyKey : "{$propertyKey}{$sideKey}";
			$formattedSideVariations[$combinedPropertyKey] = "{$combinedPropertyKey} - {$propertyLabel} {$sideLabel}";
		}
		
		return $formattedSideVariations;
	}
	
	/**
	 * Get formatted Bootstrap responsive breakpoint options
	 *
	 * Retrieves and formats available Bootstrap breakpoints for responsive spacing.
	 *
	 * @return array Formatted breakpoint options with descriptive labels
	 */
	private static function getFormattedBreakpointOptions(): array
	{
		$spacingConfiguration = getCachedReferrerList('bootstrap-spacing');
		
		$responsiveBreakpoints = $spacingConfiguration['breakpoints'] ?? [];
		
		return collect($responsiveBreakpoints)
			->map(fn ($label, $key) => "$key - $label")
			->toArray();
	}
	
	/**
	 * Get formatted Bootstrap spacing size options
	 *
	 * Retrieves available Bootstrap spacing sizes with their corresponding values.
	 *
	 * @return array Formatted size options showing both key and value
	 */
	private static function getFormattedSizeOptions(): array
	{
		$spacingConfiguration = getCachedReferrerList('bootstrap-spacing');
		$availableSizes = $spacingConfiguration['sizes'] ?? [];
		
		return collect($availableSizes)
			->map(fn ($value, $key) => "$key ($value)")
			->toArray();
	}
	
	/**
	 * Generate HTML separator field for form sections
	 *
	 * Creates a horizontal rule separator to visually divide form sections.
	 *
	 * @param string $separatorName Unique identifier for the separator field
	 * @return array Form field configuration for HTML separator
	 */
	private static function createFormSeparatorField(string $separatorName): array
	{
		return [
			'name'  => "spacing_separator_$separatorName",
			'type'  => 'custom_html',
			'value' => '<hr>',
		];
	}
	
	private static function createFormPreventHeaderOverlapField(): array
	{
		return [
			'name'  => 'prevent_header_overlap',
			'label' => trans('admin.prevent_header_overlap_label'),
			'type'  => 'checkbox_switch',
			'hint'  => trans('admin.prevent_header_overlap_hint'),
		];
	}
}
