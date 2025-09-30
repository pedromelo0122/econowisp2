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

class LocationsSection extends BaseSection
{
	public static function getFieldValues($value, $disk)
	{
		$value = is_array($value) ? $value : [];
		
		$defaultValue = [
			'show_cities'            => '1',
			'max_items'              => '19', // 14 (when 'enable_map' = 1) or 19 (when 'enable_map' = 0)
			'show_listing_btn'       => '1',
			'enable_map'             => $value['show_map'] ?? '0',
			'map_width'              => '300',
			'map_height'             => '300',
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
			'name'  => 'separator_4',
			'type'  => 'custom_html',
			'value' => trans('admin.locations_html_locations'),
		];
		
		$fields[] = [
			'name'    => 'show_cities',
			'label'   => trans('admin.Show the Country Cities'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'    => 'show_listing_btn',
			'label'   => trans('admin.Show the bottom button'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-6 cities-field',
			],
		];
		
		$fields[] = [
			'name'       => 'background_color',
			'label'      => trans('admin.Background Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#c7c5c1',
			],
			'wrapper'    => [
				'class' => 'col-md-6 cities-field',
			],
		];
		
		$fields[] = [
			'name'       => 'border_width',
			'label'      => trans('admin.Border Width'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => '1',
			],
			'wrapper'    => [
				'class' => 'col-md-6 cities-field',
			],
		];
		
		$fields[] = [
			'name'       => 'border_color',
			'label'      => trans('admin.Border Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#c7c5c1',
			],
			'wrapper'    => [
				'class' => 'col-md-6 cities-field',
			],
		];
		
		$fields[] = [
			'name'       => 'text_color',
			'label'      => trans('admin.Text Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#c7c5c1',
			],
			'wrapper'    => [
				'class' => 'col-md-6 cities-field',
			],
		];
		
		$fields[] = [
			'name'       => 'link_color',
			'label'      => trans('admin.Links Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#c7c5c1',
			],
			'wrapper'    => [
				'class' => 'col-md-6 cities-field',
			],
		];
		
		$fields[] = [
			'name'       => 'link_color_hover',
			'label'      => trans('admin.Links Color Hover'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#c7c5c1',
			],
			'wrapper'    => [
				'class' => 'col-md-6 cities-field',
			],
		];
		
		$fields[] = [
			'name'       => 'max_items',
			'label'      => trans('admin.max_cities_label'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => 12,
			],
			'hint'       => trans('admin.max_cities_hint'),
			'wrapper'    => [
				'class' => 'col-md-6 cities-field',
			],
		];
		
		$fields[] = [
			'name'    => 'items_cols',
			'label'   => trans('admin.Cities Columns'),
			'type'    => 'select2_from_array',
			'options' => [
				3 => '3',
				2 => '2',
				1 => '1',
			],
			'hint'    => trans('admin.This option is applied only when the map is displayed'),
			'wrapper' => [
				'class' => 'col-md-6 cities-field',
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
				'class' => 'col-md-6 cities-field',
			],
		];
		
		$fields[] = [
			'name'  => 'svg_map_title',
			'type'  => 'custom_html',
			'value' => trans('admin.svg_map_title'),
		];
		
		$fields[] = [
			'name'  => 'svg_map_info',
			'type'  => 'custom_html',
			'value' => trans('admin.card_light_warning', [
				'content' => trans('admin.svg_map_info', [
					'svgMapsFilesDir' => getRelativePath(config('larapen.core.maps.path')),
				]),
			]),
		];
		
		$fields[] = [
			'name'    => 'enable_map',
			'label'   => trans('admin.enable_map_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.enable_map_hint'),
			'wrapper' => [
				'class' => 'col-md-12',
			],
		];
		
		$fields[] = [
			'name'       => 'map_width',
			'label'      => trans('admin.maps_width'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => '300',
			],
			'default'    => '300',
			'wrapper'    => [
				'class' => 'col-md-6 map-field',
			],
		];
		
		$fields[] = [
			'name'       => 'map_height',
			'label'      => trans('admin.maps_height'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => '300',
			],
			'default'    => '300',
			'wrapper'    => [
				'class' => 'col-md-6 map-field',
			],
		];
		
		$fields[] = [
			'name'       => 'map_background_color',
			'label'      => trans('admin.maps_background_color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => 'transparent',
			],
			'hint'       => trans('admin.Enter a RGB color code or the word transparent'),
			'wrapper'    => [
				'class' => 'col-md-6 map-field',
			],
		];
		
		$fields[] = [
			'name'       => 'map_border',
			'label'      => trans('admin.maps_border'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#c7c5c1',
			],
			'hint'       => trans('admin.Enter a RGB color code or the word transparent'),
			'wrapper'    => [
				'class' => 'col-md-6 map-field',
			],
		];
		
		$fields[] = [
			'name'       => 'map_hover_border',
			'label'      => trans('admin.maps_hover_border'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#c7c5c1',
			],
			'wrapper'    => [
				'class' => 'col-md-6 map-field',
			],
		];
		
		$fields[] = [
			'name'       => 'map_border_width',
			'label'      => trans('admin.maps_border_width'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => 4,
			],
			'wrapper'    => [
				'class' => 'col-md-6 map-field',
			],
		];
		
		$fields[] = [
			'name'       => 'map_color',
			'label'      => trans('admin.maps_color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#f2f0eb',
			],
			'wrapper'    => [
				'class' => 'col-md-6 map-field',
			],
		];
		
		$fields[] = [
			'name'       => 'map_hover',
			'label'      => trans('admin.maps_hover'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#4682B4',
			],
			'wrapper'    => [
				'class' => 'col-md-6 map-field',
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
