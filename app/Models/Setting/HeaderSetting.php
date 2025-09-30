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

use App\Enums\BootstrapColor;

/*
 * settings.header.option
 */

class HeaderSetting
{
	public static function getFieldValues($value, $disk)
	{
		$value = is_array($value) ? $value : [];
		
		$defaultValue = [
			'full_width' => '0',
			'height'     => '65',
			
			'dark'                => '0',
			'shadow'              => '0',
			'background_class'    => 'bg-body-tertiary', // bg-body-tertiary
			'background_color'    => null, // '#f8f9fA'
			'border_bottom_width' => null, // '1px'
			'border_bottom_color' => null, // '#dee2e6'
			'animation'           => '1',
			
			'fixed_top'                 => '1',
			'height_offset'             => 200,
			'fixed_dark'                => '0',
			'fixed_shadow'              => '1',
			'fixed_background_class'    => null, // bg-primary
			'fixed_background_color'    => null,
			'fixed_border_bottom_width' => null,
			'fixed_border_bottom_color' => null,
			
			'logo_width'        => '216',
			'logo_height'       => '40',
			'logo_aspect_ratio' => '1',
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
			'name'  => 'header_sizes_title',
			'type'  => 'custom_html',
			'value' => trans('admin.header_sizes_title'),
		];
		
		$fields[] = [
			'name'    => 'full_width',
			'label'   => trans('admin.Header Full Width'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-6 mt-3',
			],
		];
		
		$fields[] = [
			'name'       => 'height',
			'label'      => trans('admin.Header Height'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => 65,
				'min'         => 0,
				'step'        => 1,
			],
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'  => 'header_style_title',
			'type'  => 'custom_html',
			'value' => trans('admin.header_style_title'),
		];
		
		$fields[] = [
			'name'    => 'dark',
			'label'   => trans('admin.dark_header_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.dark_header_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'    => 'shadow',
			'label'   => trans('admin.header_shadow_label'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		
		// Get Bootstrap's Background Colors
		$bgColorsByName = BootstrapColor::Background->getColorsByName();
		$formattedBgColors = BootstrapColor::Background->getFormattedColors();
		$fields[] = [
			'name'        => 'background_class',
			'label'       => trans('admin.header_background_class_label'),
			'type'        => 'select2_from_skins',
			'options'     => $bgColorsByName,
			'skins'       => $formattedBgColors,
			'allows_null' => true,
			'hint'        => trans('admin.header_background_class_hint'),
			'wrapper'     => [
				'class' => 'col-md-3',
			],
		];
		
		$fields[] = [
			'name'       => 'background_color',
			'label'      => trans('admin.Header Background Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#F8F8F8',
			],
			'wrapper'    => [
				'class' => 'col-md-3',
			],
		];
		
		$fields[] = [
			'name'       => 'border_bottom_width',
			'label'      => trans('admin.Header Border Bottom Width'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => 0,
				'min'         => 0,
				'step'        => 1,
			],
			'wrapper'    => [
				'class' => 'col-md-3',
			],
		];
		
		$fields[] = [
			'name'       => 'border_bottom_color',
			'label'      => trans('admin.Header Border Bottom Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#E8E8E8',
			],
			'wrapper'    => [
				'class' => 'col-md-3',
			],
		];
		
		$fields[] = [
			'name'       => 'link_color',
			'label'      => trans('admin.Header Links Color'),
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
			'label'      => trans('admin.Header Links Color Hover'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#000',
			],
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'    => 'animation',
			'label'   => trans('admin.header_animation_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.header_animation_hint'),
			'wrapper' => [
				'class' => 'col-md-6',
			],
			'newline' => true,
		];
		
		$fields[] = [
			'name'  => 'fixed_header_style_title',
			'type'  => 'custom_html',
			'value' => trans('admin.fixed_header_style_title'),
		];
		
		$fields[] = [
			'name'    => 'fixed_top',
			'label'   => trans('admin.header_fixed_top_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.header_fixed_top_hint', ['navbarHeightOffset' => trans('admin.header_height_offset_label')]),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'       => 'height_offset',
			'label'      => trans('admin.header_height_offset_label'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => 200,
				'min'         => 0,
				'step'        => 1,
			],
			'hint'       => trans('admin.header_height_offset_hint'),
			'wrapper'    => [
				'class' => 'col-md-6 fixed-header',
			],
		];
		
		$fields[] = [
			'name'    => 'fixed_dark',
			'label'   => trans('admin.fixed_dark_header_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.fixed_dark_header_hint'),
			'wrapper' => [
				'class' => 'col-md-6 fixed-header',
			],
		];
		
		$fields[] = [
			'name'    => 'fixed_shadow',
			'label'   => trans('admin.fixed_header_shadow_label'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-6 fixed-header',
			],
		];
		
		$fields[] = [
			'name'        => 'fixed_background_class',
			'label'       => trans('admin.fixed_header_background_class_label'),
			'type'        => 'select2_from_skins',
			'options'     => $bgColorsByName,
			'skins'       => $formattedBgColors,
			'allows_null' => true,
			'hint'        => trans('admin.fixed_header_background_class_hint'),
			'wrapper'     => [
				'class' => 'col-md-3 fixed-header',
			],
		];
		
		$fields[] = [
			'name'       => 'fixed_background_color',
			'label'      => trans('admin.fixed_header_background_color_label'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#F8F8F8',
			],
			'wrapper'    => [
				'class' => 'col-md-3 fixed-header',
			],
		];
		
		$fields[] = [
			'name'       => 'fixed_border_bottom_width',
			'label'      => trans('admin.fixed_header_border_bottom_width_label'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => 0,
				'min'         => 0,
				'step'        => 1,
			],
			'wrapper'    => [
				'class' => 'col-md-3 fixed-header',
			],
		];
		
		$fields[] = [
			'name'       => 'fixed_border_bottom_color',
			'label'      => trans('admin.fixed_header_border_bottom_color_label'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#E8E8E8',
			],
			'wrapper'    => [
				'class' => 'col-md-3 fixed-header',
			],
		];
		
		$fields[] = [
			'name'       => 'fixed_link_color',
			'label'      => trans('admin.fixed_header_link_color_label'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#333',
			],
			'wrapper'    => [
				'class' => 'col-md-6 fixed-header',
			],
		];
		
		$fields[] = [
			'name'       => 'fixed_link_color_hover',
			'label'      => trans('admin.fixed_header_link_color_hover_label'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#000',
			],
			'wrapper'    => [
				'class' => 'col-md-6 fixed-header',
			],
			'newline'    => true,
		];
		
		$fields[] = [
			'name'  => 'header_logo_title',
			'type'  => 'custom_html',
			'value' => trans('admin.header_logo_title'),
		];
		
		$fields[] = [
			'name'       => 'logo_width',
			'label'      => trans('admin.logo_width_label'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => 216,
				'min'         => 0,
				'step'        => 1,
			],
			'hint'       => trans('admin.logo_width_hint'),
			'wrapper'    => [
				'class' => 'col-md-3',
			],
		];
		
		$fields[] = [
			'name'       => 'logo_height',
			'label'      => trans('admin.logo_height_label'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => 40,
				'min'         => 0,
				'step'        => 1,
			],
			'hint'       => trans('admin.logo_height_hint'),
			'wrapper'    => [
				'class' => 'col-md-3',
			],
		];
		
		$fields[] = [
			'name'    => 'logo_aspect_ratio',
			'label'   => trans('admin.logo_aspect_ratio_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.logo_aspect_ratio_hint'),
			'wrapper' => [
				'class' => 'col-md-6 mt-3',
			],
		];
		
		return addOptionsGroupJavaScript(__NAMESPACE__, __CLASS__, $fields);
	}
}
