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

use App\Helpers\Common\Files\Upload;
use App\Models\Language;
use App\Models\Section\BaseSection;
use Illuminate\Support\Facades\Storage;

class SearchFormSection extends BaseSection
{
	public static function passedValidation($request)
	{
		$params = [
			[
				'attribute' => 'background_image_path',
				'destPath'  => 'app/logo',
				'width'     => (int)config('larapen.media.resize.namedOptions.bg-header.width', 2000),
				'height'    => (int)config('larapen.media.resize.namedOptions.bg-header.height', 1000),
				'ratio'     => config('larapen.media.resize.namedOptions.bg-header.ratio', '1'),
				'upsize'    => config('larapen.media.resize.namedOptions.bg-header.upsize', '0'),
				'filename'  => 'section-header-',
				'quality'   => 100,
			],
		];
		
		foreach ($params as $param) {
			$file = $request->hasFile($param['attribute'])
				? $request->file($param['attribute'])
				: $request->input($param['attribute']);
			
			$request->request->set($param['attribute'], Upload::image($file, $param['destPath'], $param));
		}
		
		return $request;
	}
	
	public static function getFieldValues($value, $disk)
	{
		$value = is_array($value) ? $value : [];
		
		$cacheExpiration = (int)config('settings.optimization.cache_expiration', 3600);
		$cacheId = 'models:languages.active';
		$languages = cache()->remember($cacheId, $cacheExpiration, function () {
			return Language::active()->get();
		});
		
		$defaultValue = [
			'enable_extended_form_area' => '1',
			'background_image_path'     => null,
			'background_image_darken'   => 0.0,
			'margins'                   => self::getDefaultMarginConfiguration(),
			'prevent_header_overlap'    => '1',
		];
		
		if ($languages->count() > 0) {
			foreach ($languages as $language) {
				$title = $value['title_' . $language->code] ?? t('homepage_title_text', [], 'global', $language->code);
				$subTitle = $value['sub_title_' . $language->code] ?? t('simple_fast_and_efficient', [], 'global', $language->code);
				
				$value['title_' . $language->code] = $title;
				$value['sub_title_' . $language->code] = $subTitle;
			}
		}
		
		$value = array_merge($defaultValue, $value);
		
		/** @var $disk Storage */
		$filePathList = ['background_image_path'];
		foreach ($value as $key => $item) {
			if (in_array($key, $filePathList)) {
				if (empty($item) || !$disk->exists($item)) {
					$value[$key] = $defaultValue[$key] ?? null;
				}
			}
		}
		
		// Append files URLs
		// background_image_url
		$backgroundImage = $value['background_image_path'] ?? $value['background_image'] ?? null;
		$value['background_image_url'] = thumbService($backgroundImage, false)->resize('bg-header')->url();
		
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
			'name'    => 'enable_extended_form_area',
			'label'   => trans('admin.enable_extended_form_area_label'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-12',
			],
			'hint'    => trans('admin.enable_extended_form_area_hint'),
		];
		
		$fields[] = [
			'name'    => 'separator_1',
			'type'    => 'custom_html',
			'value'   => trans('admin.search_form_html_background'),
			'wrapper' => [
				'class' => 'col-md-12 extended',
			],
		];
		
		$fields[] = [
			'name'       => 'background_color',
			'label'      => trans('admin.Background Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#444',
			],
			'hint'       => trans('admin.Enter a RGB color code'),
			'wrapper'    => [
				'class' => 'col-md-12 extended',
			],
		];
		
		$fields[] = [
			'name'    => 'background_image_path',
			'label'   => trans('admin.Background Image'),
			'type'    => 'image',
			'upload'  => true,
			'disk'    => $diskName,
			'hint'    => trans('admin.search_form_background_image_hint'),
			'wrapper' => [
				'class' => 'col-md-12 extended',
			],
		];
		
		$fields[] = [
			'name'       => 'background_image_darken',
			'label'      => trans('admin.background_image_darken_label'),
			'type'       => 'range',
			'attributes' => [
				'placeholder' => '0.5',
				'min'         => 0,
				'max'         => 1,
				'step'        => 0.05,
				'style'       => 'padding: 0;',
			],
			'default'    => 0,
			'hint'       => trans('admin.background_image_darken_hint'),
			'wrapper'    => [
				'class' => 'col-md-4 extended',
			],
		];
		
		$fields[] = [
			'name'       => 'height',
			'label'      => trans('admin.Height'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => '450',
				'min'         => 45,
				'max'         => 2000,
				'step'        => 1,
			],
			'hint'       => trans('admin.Enter a value greater than 50px'),
			'wrapper'    => [
				'class' => 'col-md-4 extended',
			],
		];
		
		$fields[] = [
			'name'    => 'parallax',
			'label'   => trans('admin.Enable Parallax Effect'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-4 mt-3 extended',
			],
		];
		
		$fields[] = [
			'name'  => 'separator_2',
			'type'  => 'custom_html',
			'value' => trans('admin.search_form_html_search_form'),
		];
		
		$fields[] = [
			'name'    => 'hide_form',
			'label'   => trans('admin.Hide the Form'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-12 extended',
			],
		];
		
		$fields[] = [
			'name'       => 'form_border_color',
			'label'      => trans('admin.Form Border Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#333',
			],
			'hint'       => trans('admin.Enter a RGB color code'),
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'       => 'form_border_width',
			'label'      => trans('admin.Form Border Width'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => '5',
				'min'         => 0,
				'max'         => 10,
				'step'        => 1,
			],
			'hint'       => trans('admin.Enter a number with unit'),
			'wrapper'    => [
				'class' => 'col-md-3',
			],
		];
		
		$fields[] = [
			'name'       => 'form_border_radius',
			'label'      => trans('admin.Form Border Radius'),
			'type'       => 'number',
			'attributes' => [
				'placeholder' => '5',
				'min'         => 0,
				'max'         => 30,
				'step'        => 1,
			],
			'hint'       => trans('admin.Enter a number with unit'),
			'wrapper'    => [
				'class' => 'col-md-3',
			],
		];
		
		$fields[] = [
			'name'       => 'form_btn_background_color',
			'label'      => trans('admin.Form Button Background Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#4682B4',
			],
			'hint'       => trans('admin.Enter a RGB color code'),
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'       => 'form_btn_text_color',
			'label'      => trans('admin.Form Button Text Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#FFF',
			],
			'hint'       => trans('admin.Enter a RGB color code'),
			'wrapper'    => [
				'class' => 'col-md-6',
			],
		];
		
		$fields[] = [
			'name'    => 'separator_3',
			'type'    => 'custom_html',
			'value'   => trans('admin.search_form_html_titles'),
			'wrapper' => [
				'class' => 'col-md-12 extended',
			],
		];
		
		$fields[] = [
			'name'    => 'hide_titles',
			'label'   => trans('admin.Hide Titles'),
			'type'    => 'checkbox_switch',
			'wrapper' => [
				'class' => 'col-md-12 extended',
			],
		];
		
		$fields[] = [
			'name'    => 'separator_3_1',
			'type'    => 'custom_html',
			'value'   => trans('admin.search_form_html_titles_content'),
			'wrapper' => [
				'class' => 'col-md-12 extended',
			],
		];
		
		$fields[] = [
			'name'    => 'separator_3_2',
			'type'    => 'custom_html',
			'value'   => trans('admin.dynamic_variables_stats_hint'),
			'wrapper' => [
				'class' => 'col-md-12 extended',
			],
		];
		
		$languages = Language::query()->active()->get();
		if ($languages->count() > 0) {
			foreach ($languages as $language) {
				$fields[] = [
					'name'       => 'title_' . $language->code,
					'label'      => mb_ucfirst(trans('admin.title')) . ' (' . $language->name . ')',
					'attributes' => [
						'placeholder' => t('homepage_title_text', [], 'global', $language->code),
					],
					'wrapper'    => [
						'class' => 'col-md-6 extended',
					],
				];
				$fields[] = [
					'name'       => 'sub_title_' . $language->code,
					'label'      => trans('admin.Sub Title') . ' (' . $language->name . ')',
					'attributes' => [
						'placeholder' => t('simple_fast_and_efficient', [], 'global', $language->code),
					],
					'wrapper'    => [
						'class' => 'col-md-6 extended',
					],
				];
			}
		}
		
		$fields[] = [
			'name'    => 'separator_3_3',
			'type'    => 'custom_html',
			'value'   => trans('admin.search_form_html_titles_color'),
			'wrapper' => [
				'class' => 'col-md-12 extended',
			],
		];
		
		$fields[] = [
			'name'       => 'big_title_color',
			'label'      => trans('admin.Big Title Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#FFF',
			],
			'hint'       => trans('admin.Enter a RGB color code'),
			'wrapper'    => [
				'class' => 'col-md-6 extended',
			],
		];
		
		$fields[] = [
			'name'       => 'sub_title_color',
			'label'      => trans('admin.Sub Title Color'),
			'type'       => 'color_picker',
			'attributes' => [
				'placeholder' => '#FFF',
			],
			'hint'       => trans('admin.Enter a RGB color code'),
			'wrapper'    => [
				'class' => 'col-md-6 extended',
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
