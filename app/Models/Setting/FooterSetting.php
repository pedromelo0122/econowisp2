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

/*
 * settings.footer.option
 */

class FooterSetting
{
	public static function getFieldValues($value, $disk)
	{
		$value = is_array($value) ? $value : [];
		
		$iosAppUrl = config('settings.other.ios_app_url');
		$androidAppUrl = config('settings.other.android_app_url');
		
		$defaultValue = [
			'dark'             => '1',
			'high_spacing'     => '1',
			'full_width'       => '0',
			'background_color' => null, // '#f8f9fA'
			'border_top_width' => null, // '1px'
			'border_top_color' => null, // '#dee2e6'
			
			'hide_payment_plugins_logos' => '1',
			'ios_app_url'                => $iosAppUrl ?? null,
			'android_app_url'            => $androidAppUrl ?? null,
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
			'name'  => 'style_footer_title',
			'type'  => 'custom_html',
			'value' => trans('admin.style_footer_title'),
		];
		$fields[] = [
			'name'    => 'dark',
			'label'   => trans('admin.dark_footer_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.dark_footer_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'high_spacing',
			'label'   => trans('admin.high_spacing_footer_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.high_spacing_footer_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'full_width',
			'label'   => trans('admin.Footer Full Width'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-12',
			],
		];
		$fields[] = [
			'name'       => 'background_color',
			'label'      => trans('admin.Footer Background Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#F5F5F5',
			],
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'border_top_width',
			'label'   => trans('admin.Footer Border Top Width'),
			'type'    => 'number',
			'wrapper' => [
				'class' => 'col-md-3',
			],
		];
		$fields[] = [
			'name'       => 'border_top_color',
			'label'      => trans('admin.Footer Border Top Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#E8E8E8',
			],
			'wrapper'    => [
				'class' => 'col-md-3',
			],
		];
		$fields[] = [
			'name'       => 'text_color',
			'label'      => trans('admin.Footer Text Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#333',
			],
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'       => 'title_color',
			'label'      => trans('admin.Footer Titles Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#000',
			],
			'wrapper'    => [
				'class' => 'col-md-6',
			],
			'newline'    => true,
		];
		
		$fields[] = [
			'name'       => 'link_color',
			'label'      => trans('admin.Footer Links Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#333',
			],
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'       => 'link_color_hover',
			'label'      => trans('admin.Footer Links Color Hover'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#333',
			],
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'       => 'inside_line_border_color',
			'label'      => trans('admin.footer_inside_line_border_color_label'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#ddd',
			],
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'  => 'footer_elements_ctrl_title',
			'type'  => 'custom_html',
			'value' => trans('admin.footer_elements_ctrl_title'),
		];
		$fields[] = [
			'name'    => 'hide_links',
			'label'   => trans('admin.Hide Links'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'hide_payment_plugins_logos',
			'label'   => trans('admin.Hide Payment Plugins Logos'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'  => 'mobile_apps_title',
			'type'  => 'custom_html',
			'value' => trans('admin.mobile_apps_title'),
		];
		$fields[] = [
			'name'    => 'ios_app_url',
			'label'   => trans('admin.app_store_label'),
			'type'    => 'text',
			'hint'    => trans('admin.available_on_app_store_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'android_app_url',
			'label'   => trans('admin.google_play_label'),
			'type'    => 'text',
			'hint'    => trans('admin.available_on_google_play_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'  => 'powered_by_title',
			'type'  => 'custom_html',
			'value' => trans('admin.powered_by_title'),
		];
		$fields[] = [
			'name'    => 'hide_powered_by',
			'label'   => trans('admin.hide_powered_by_label'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-6 mt-3',
			],
		];
		$fields[] = [
			'name'    => 'powered_by_text',
			'label'   => trans('admin.powered_by_text_label'),
			'type'    => 'text',
			'wrapper' => [
				'class' => 'col-md-6 powered-by-field',
			],
			'newline' => true,
		];
		
		$fields[] = [
			'name'  => 'tracking_title',
			'type'  => 'custom_html',
			'value' => trans('admin.tracking_title'),
		];
		$fields[] = [
			'name'       => 'tracking_code',
			'label'      => trans('admin.tracking_code_label'),
			'type'       => 'textarea',
			'attributes' => [
				'rows' => '15',
			],
			'hint'       => trans('admin.tracking_code_hint'),
			'wrapper'    => [
				'class' => 'col-md-12',
			],
		];
		
		return addOptionsGroupJavaScript(__NAMESPACE__, __CLASS__, $fields);
	}
}
