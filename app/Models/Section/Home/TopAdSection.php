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

namespace App\Models\Section\Home;

use App\Models\Section\BaseSection;

class TopAdSection extends BaseSection
{
	public static function getFieldValues($value, $disk)
	{
		$value = is_array($value) ? $value : [];
		
		$defaultValue = [
			'margins'                => self::getDefaultMarginConfiguration(),
			'prevent_header_overlap' => '1',
		];
		
		$value = array_merge($defaultValue, $value);
		
		// Build CSS class list based on defined options
		$value['css_classes'] = self::buildCssClasses($value);
		
		return $value;
	}
	
	public static function setFieldValues($value, $setting)
	{
		return $value;
	}
	
	public static function getFields($diskName): array
	{
		$fields = [];
		
		$fields = self::appendSpacingFormFields($fields, fieldSeparator: 'end');
		
		$fields[] = [
			'name'     => 'active',
			'label'    => trans('admin.Active'),
			'type'     => 'checkbox_switch',
			'hint'     => trans('admin.top_ad_active_hint'),
			'fake'     => false,
			'store_in' => null,
		];
		
		return $fields;
	}
}
