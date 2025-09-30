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

namespace App\Models\Setting;

use Larapen\LaravelDistance\Libraries\mysql\DistanceHelper;

/*
 * settings.listings_list.option
 */

class ListingsListSetting
{
	public static function getFieldValues($value, $disk)
	{
		$value = is_array($value) ? $value : [];
		
		$defaultDistanceCalculationFormula = DistanceHelper::getDefaultDistanceCalculationFunction();
		
		$defaultValue = [
			'display_browse_listings_link' => '0',
			'display_mode'                 => 'grid-view',
			'show_left_sidebar'            => '1',
			'left_sidebar_offcanvas'       => 'md',
			'min_price'                    => '0',
			'max_price'                    => '10000',
			'price_slider_step'            => '50',
			'show_category_icon'           => '7',
			'enable_cities_autocompletion' => '1',
			'enable_diacritics'            => '0',
			'cities_extended_searches'     => '1',
			'distance_calculation_formula' => $defaultDistanceCalculationFormula,
			'search_distance_max'          => '500',
			'search_distance_default'      => '50',
			'search_distance_interval'     => '100',
			'premium_first'                => '0',
			'premium_first_category'       => '1',
			'premium_first_location'       => '1',
			'free_listings_in_premium'     => '0',
			'date_from_now'                => $value['elapsed_time_from_now'] ?? null, // from old saved value
		];
		
		return array_merge($defaultValue, $value);
	}
	
	public static function setFieldValues($value, $setting)
	{
		return $value;
	}
	
	public static function getFields($diskName): array
	{
		$fields = [];
		
		$fields[] = [
			'name'  => 'separator_1',
			'type'  => 'custom_html',
			'value' => trans('admin.list_html_displaying'),
		];
		$fields[] = [
			'name'    => 'display_browse_listings_link',
			'label'   => trans('admin.browse_listings_link_in_header_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.browse_listings_link_in_header_hint'),
			'wrapper' => [
				'class' => 'col-md-6 mb-4',
			],
		];
		$fields[] = [
			'name'    => 'display_states_search_tip',
			'label'   => trans('admin.display_states_search_tip_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.display_states_search_tip_hint'),
			'wrapper' => [
				'class' => 'col-md-6 mb-4',
			],
		];
		$fields[] = [
			'name'    => 'display_mode',
			'label'   => trans('admin.Listing Page Display Mode'),
			'type'    => 'select2_from_array',
			'options' => collect(getDisplayModeList())
				->flip()
				->map(fn ($item) => ucfirst($item))
				->sort()
				->toArray(),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'grid_view_cols',
			'label'   => trans('admin.Grid View Columns'),
			'type'    => 'select2_from_array',
			'options' => [
				4 => '4',
				3 => '3',
				2 => '2',
			],
			'wrapper' => [
				'class' => 'col-md-6 grid-view',
			],
		];
		$fields[] = [
			'name'    => 'fake_locations_results',
			'label'   => trans('admin.fake_locations_results_label'),
			'type'    => 'select2_from_array',
			'options' => [
				0 => trans('admin.fake_locations_results_op_1'),
				1 => trans('admin.fake_locations_results_op_2'),
				2 => trans('admin.fake_locations_results_op_3'),
			],
			'hint'    => trans('admin.fake_locations_results_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
			'newline' => true,
		];
		
		$fields[] = [
			'name'    => 'show_cats_in_top',
			'label'   => trans('admin.show_cats_in_top_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.show_cats_in_top_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'show_category_icon',
			'label'   => trans('admin.show_category_icon_label'),
			'type'    => 'select2_from_array',
			'options' => [
				1 => trans('admin.show_category_icon_op_1'),
				2 => trans('admin.show_category_icon_op_2'),
				3 => trans('admin.show_category_icon_op_3'),
				4 => trans('admin.show_category_icon_op_4'),
				5 => trans('admin.show_category_icon_op_5'),
				6 => trans('admin.show_category_icon_op_6'),
				7 => trans('admin.show_category_icon_op_7'),
				8 => trans('admin.show_category_icon_op_8'),
			],
			'hint'    => trans('admin.show_category_icon_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
			'newline' => true,
		];
		$fields[] = [
			'name'    => 'show_left_sidebar',
			'label'   => trans('admin.show_left_sidebar_label'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'left_sidebar_offcanvas',
			'label'   => trans('admin.left_sidebar_offcanvas_label'),
			'type'    => 'select2_from_array',
			'options' => getSerpOffcanvasBreakpoint(),
			'hint'    => trans('admin.left_sidebar_offcanvas_hint'),
			'wrapper' => [
				'class' => 'col-md-6 show-search-sidebar',
			],
			'newline' => true,
		];
		$fields[] = [
			'name'    => 'show_listings_tags',
			'label'   => trans('admin.show_listings_tags_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.show_listings_tags_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
			'newline' => true,
		];
		
		$fields[] = [
			'name'       => 'min_price',
			'label'      => trans('admin.min_price_label'),
			'type'       => 'number',
			'required'   => true,
			'attributes' => [
				'min'  => 0,
				'step' => 1,
			],
			'hint'       => trans('admin.min_price_hint'),
			'wrapper'    => [
				'class' => 'col-lg-4 col-md-6',
			],
		];
		$fields[] = [
			'name'       => 'max_price',
			'label'      => trans('admin.max_price_label'),
			'type'       => 'number',
			'required'   => true,
			'attributes' => [
				'min'  => 1,
				'step' => 1,
			],
			'hint'       => trans('admin.max_price_hint'),
			'wrapper'    => [
				'class' => 'col-lg-4 col-md-6',
			],
		];
		$fields[] = [
			'name'       => 'price_slider_step',
			'label'      => trans('admin.price_slider_step_label'),
			'type'       => 'number',
			'required'   => true,
			'attributes' => [
				'min'  => 1,
				'step' => 1,
			],
			'hint'       => trans('admin.price_slider_step_hint'),
			'wrapper'    => [
				'class' => 'col-lg-4 col-md-6',
			],
		];
		
		$fields[] = [
			'name'  => 'count_listings_sep',
			'type'  => 'custom_html',
			'value' => trans('admin.count_listings_title'),
		];
		$fields[] = [
			'name'    => 'count_categories_listings',
			'label'   => trans('admin.count_categories_listings_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.count_categories_listings_hint', [
				'extendedSearches' => trans('admin.cities_extended_searches_label'),
			]),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'count_cities_listings',
			'label'   => trans('admin.count_cities_listings_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.count_cities_listings_hint', [
				'extendedSearches' => trans('admin.cities_extended_searches_label'),
			]),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'  => 'dates_sep',
			'type'  => 'custom_html',
			'value' => trans('admin.dates_title'),
		];
		$fields[] = [
			'name'    => 'hide_date',
			'label'   => trans('admin.hide_date_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.listing_hide_date_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'php_specific_date_format',
			'type'    => 'custom_html',
			'value'   => trans('admin.php_specific_date_format_info'),
			'wrapper' => [
				'class' => 'col-md-12 date-field',
			],
		];
		$fields[] = [
			'name'    => 'date_from_now',
			'label'   => trans('admin.date_from_now_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.listing_date_from_now_hint', [
				'app'          => trans('settings.app'),
				'appUrl'       => urlGen()->adminUrl('settings/find/app'),
				'languagesUrl' => urlGen()->adminUrl('languages'),
			]),
			'wrapper' => [
				'class' => 'col-md-12 date-field',
			],
		];
		
		$fields[] = [
			'name'  => 'listing_info',
			'type'  => 'custom_html',
			'value' => trans('admin.listing_info_title'),
		];
		$fields[] = [
			'name'  => 'listing_info_description',
			'type'  => 'custom_html',
			'value' => trans('admin.listing_info_description'),
		];
		$fields[] = [
			'name'    => 'hide_post_type',
			'label'   => trans('admin.hide_post_type_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.hide_post_type_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'hide_category',
			'label'   => trans('admin.hide_category_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.hide_category_hint')
				. '<br>'
				. trans('admin.hide_category_hint_note', ['defaultValue' => t('Contact us')]),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'hide_location',
			'label'   => trans('admin.hide_location_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.hide_location_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'  => 'search_title',
			'type'  => 'custom_html',
			'value' => trans('admin.search_title'),
		];
		$fields[] = [
			'name'    => 'enable_cities_autocompletion',
			'label'   => trans('admin.enable_cities_autocompletion_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.enable_cities_autocompletion_hint'),
			'wrapper' => [
				'class' => 'col-md-12',
			],
		];
		$fields[] = [
			'name'    => 'enable_diacritics',
			'label'   => trans('admin.enable_diacritics_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.enable_diacritics_hint'),
			'wrapper' => [
				'class' => 'col-md-12',
			],
		];
		
		$fields[] = [
			'name'  => 'extended_searches_title',
			'type'  => 'custom_html',
			'value' => trans('admin.extended_searches_title'),
		];
		$fields[] = [
			'name'    => 'cities_extended_searches',
			'label'   => trans('admin.cities_extended_searches_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.cities_extended_searches_hint'),
			'wrapper' => [
				'class' => 'col-md-12',
			],
		];
		$fields[] = [
			'name'    => 'distance_calculation_formula',
			'label'   => trans('admin.distance_calculation_formula_label'),
			'type'    => 'select2_from_array',
			'options' => DistanceHelper::getDistanceCalculationFormula(),
			'hint'    => trans('admin.distance_calculation_formula_hint'),
			'wrapper' => [
				'class' => 'col-md-6 extended-searches',
			],
		];
		$fields[] = [
			'name'    => 'search_distance_default',
			'label'   => trans('admin.Default Search Distance'),
			'type'    => 'select2_from_array',
			'options' => [
				200 => '200',
				100 => '100',
				50  => '50',
				25  => '25',
				20  => '20',
				10  => '10',
				0   => '0',
			],
			'hint'    => trans('admin.Default search radius distance'),
			'wrapper' => [
				'class' => 'col-md-6 extended-searches',
			],
		];
		$fields[] = [
			'name'    => 'separator_3',
			'type'    => 'custom_html',
			'value'   => '<div style="clear: both;"></div>',
			'wrapper' => [
				'class' => 'col-md-12 extended-searches',
			],
		];
		$fields[] = [
			'name'    => 'search_distance_max',
			'label'   => trans('admin.Max Search Distance'),
			'type'    => 'select2_from_array',
			'options' => [
				1000 => '1000',
				900  => '900',
				800  => '800',
				700  => '700',
				600  => '600',
				500  => '500',
				400  => '400',
				300  => '300',
				200  => '200',
				100  => '100',
				50   => '50',
				0    => '0',
			],
			'hint'    => trans('admin.Max search radius distance'),
			'wrapper' => [
				'class' => 'col-md-6 extended-searches',
			],
		];
		$fields[] = [
			'name'    => 'search_distance_interval',
			'label'   => trans('admin.Distance Interval'),
			'type'    => 'select2_from_array',
			'options' => [
				250 => '250',
				200 => '200',
				100 => '100',
				50  => '50',
				25  => '25',
				20  => '20',
				10  => '10',
				5   => '5',
			],
			'hint'    => trans('admin.The interval between filter distances'),
			'wrapper' => [
				'class' => 'col-md-6 extended-searches',
			],
		];
		
		$fields[] = [
			'name'  => 'premium_listings',
			'type'  => 'custom_html',
			'value' => trans('admin.premium_listings'),
		];
		$fields[] = [
			'name'  => 'premium_listings_notes',
			'type'  => 'custom_html',
			'value' => trans('admin.premium_listings_notes'),
		];
		$fields[] = [
			'name'  => 'premium_listings_in_searches_title',
			'type'  => 'custom_html',
			'value' => trans('admin.premium_listings_in_searches_title'),
		];
		$fields[] = [
			'name'    => 'premium_first',
			'label'   => trans('admin.premium_first_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.premium_first_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'premium_first_category',
			'label'   => trans('admin.premium_first_category_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.premium_first_category_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'premium_first_location',
			'label'   => trans('admin.premium_first_location_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.premium_first_location_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'  => 'free_listings_in_premium_title',
			'type'  => 'custom_html',
			'value' => trans('admin.free_listings_in_premium_title'),
		];
		$fields[] = [
			'name'    => 'free_listings_in_premium',
			'label'   => trans('admin.free_listings_in_premium_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.free_listings_in_premium_hint'),
			'wrapper' => [
				'class' => 'col-md-12',
			],
		];
		
		return addOptionsGroupJavaScript(__NAMESPACE__, __CLASS__, $fields);
	}
}
