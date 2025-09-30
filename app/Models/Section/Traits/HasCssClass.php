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

trait HasCssClass
{
	use HasSpacing;
	
	/**
	 * Generate the global CSS classes from configuration data
	 *
	 * Converts stored:
	 * - margin/padding configuration
	 * - and visibility settings
	 * into valid Bootstrap CSS classes for frontend rendering.
	 *
	 * @param array $data e.g. Configuration data containing margin and visibility settings
	 * @param string $targetProperty Bootstrap property prefix (m for margin, p for padding, g for gap)
	 * @return string Space-separated string of Bootstrap CSS classes
	 */
	public static function buildCssClasses(array $data, string $targetProperty = 'm'): string
	{
		// Process spacing configuration
		// Margin classes (e.g. mb-4 or mb-lg-4 or mb-md-4)
		$marginConfig = $data['margins'] ?? [];
		$cssClasses = self::buildSpacingClasses($marginConfig, $targetProperty);
		$cssClasses = collect($cssClasses)->unique()->toArray();
		
		// Prevent Header Overlap
		$preventHeaderOverlap = $data['prevent_header_overlap'] ?? '0';
		if ($preventHeaderOverlap == '1') {
			$cssClasses[] = 'prevent-header-overlap';
		}
		
		// Handle responsive visibility
		$hiddenOnMobile = $data['hide_on_mobile'] ?? '0';
		if ($hiddenOnMobile === '1') {
			$cssClasses[] = 'd-none d-md-block';
		}
		
		return implode(' ', $cssClasses);
	}
}
