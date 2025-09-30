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

class CategoriesSection extends BaseSection
{
	public static function getFieldValues($value, $disk)
	{
		$value = is_array($value) ? $value : [];
		
		$defaultCatDisplayType = 'c_bigIcon_list';
		
		$defaultValue = [
			'max_items'              => null, // i.e. Show all root categories
			'cat_display_type'       => $defaultCatDisplayType,
			'show_icon'              => '1',
			'max_sub_cats'           => '3',
			'margins'                => self::getDefaultMarginConfiguration(),
			'prevent_header_overlap' => '1',
		];
		
		$value = array_merge($defaultValue, $value);
		
		// Validate categories display type
		$catDisplayType = $value['cat_display_type'] ?? null;
		if (!empty($catDisplayType) && in_array($catDisplayType, ['c_circle_list', 'c_check_list'])) {
			$value['cat_display_type'] = $defaultCatDisplayType;
		}
		
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
			'name'        => 'cat_display_type',
			'label'       => trans('admin.cat_display_type_label'),
			'type'        => 'select2_from_array',
			'options'     => [
				'c_normal_list'    => trans('admin.cat_display_type_op_1'),
				'c_border_list'    => trans('admin.cat_display_type_op_2'),
				'c_bigIcon_list'   => trans('admin.cat_display_type_op_3'),
				'c_picture_list'   => trans('admin.cat_display_type_op_4'),
				'cc_normal_list'   => trans('admin.cat_display_type_op_5'),
				'cc_normal_list_s' => trans('admin.cat_display_type_op_6'),
			],
			'allows_null' => false,
			'hint'        => trans('admin.cat_display_type_hint', [
				'type_1' => trans('admin.cat_display_type_op_5'),
				'type_2' => trans('admin.cat_display_type_op_6'),
			]),
			'wrapper'     => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'    => 'max_items',
			'label'   => trans('admin.max_categories_label'),
			'type'    => 'number',
			'hint'    => trans('admin.max_categories_hint'),
			'wrapper' => [
				'class' => 'col-md-6 normal-type',
			],
			'newline' => true,
		];
		
		$fields[] = [
			'name'    => 'max_sub_cats',
			'label'   => trans('admin.Max subcategories displayed by default'),
			'type'    => 'number',
			'hint'    => trans('admin.max_sub_cats_hint'),
			'wrapper' => [
				'class' => 'col-md-6 nested-type',
			],
		];
		
		$fields[] = [
			'name'       => 'cache_expiration',
			'label'      => trans('admin.Cache Expiration Time for this section'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => '0',
			],
			'hint'       => trans('admin.section_cache_expiration_hint'),
			'wrapper'    => [
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
		
		return addOptionsGroupJavaScript(__NAMESPACE__, __CLASS__, $fields);
	}
}
