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

class LatestListingsSection extends BaseSection
{
	public static function getFieldValues($value, $disk)
	{
		$value = is_array($value) ? $value : [];
		
		$defaultValue = [
			'max_items'              => '8',
			'show_view_more_btn'     => '1',
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
		
		$fields[] = [
			'name'       => 'max_items',
			'label'      => trans('admin.Max Items'),
			'type'       => 'number',
			'attributes' => [
				'min'  => 1,
				'max'  => 100,
				'step' => 1,
			],
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'        => 'order_by',
			'label'       => trans('admin.Order By'),
			'type'        => 'select2_from_array',
			'options'     => [
				'date'   => 'Date',
				'random' => 'Random',
			],
			'allows_null' => false,
			'wrapper'     => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'    => 'items_in_carousel',
			'label'   => trans('admin.items_in_carousel_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.items_in_carousel_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'       => 'cache_expiration',
			'label'      => trans('admin.Cache Expiration Time for this section'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => '0',
				'min'         => 0,
				'step'        => 1,
			],
			'hint'       => trans('admin.section_cache_expiration_hint'),
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'    => 'show_view_more_btn',
			'label'   => trans('admin.Show View More Button'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.show_view_more_btn_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		
		$fields = self::appendSpacingFormFields($fields, fieldSeparator: 'both');
		
		$fields[] = [
			'name'  => 'hide_on_mobile',
			'label' => trans('admin.hide_on_mobile_label'),
			'type'  => 'checkbox_switch',
			'hint'  => trans('admin.hide_on_mobile_hint'),
		];
		
		$fields[] = [
			'name'     => 'active',
			'label'    => trans('admin.Active'),
			'type'     => 'checkbox_switch',
			'fake'     => false,
			'store_in' => null,
		];
		
		return $fields;
	}
}
