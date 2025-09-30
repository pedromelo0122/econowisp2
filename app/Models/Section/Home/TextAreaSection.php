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

use App\Models\Language;
use App\Models\Section\BaseSection;

class TextAreaSection extends BaseSection
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
		$wysiwygEditor = config('settings.other.wysiwyg_editor');
		$wysiwygEditorViewPath = 'views/admin/panel/fields/' . $wysiwygEditor . '.blade.php';
		
		$fields = [];
		
		$fields[] = [
			'name'  => 'dynamic_variables_hint',
			'type'  => 'custom_html',
			'value' => trans('admin.dynamic_variables_hint'),
		];
		
		$languages = Language::query()->active()->get();
		if ($languages->count() > 0) {
			foreach ($languages as $language) {
				$titleLabel = mb_ucfirst(trans('admin.title')) . ' (' . $language->name . ')';
				$bodyLabel = trans('admin.body_label') . ' (' . $language->name . ')';
				
				$fields[] = [
					'name'       => 'title_' . $language->code,
					'label'      => $titleLabel,
					'type'       => 'text',
					'attributes' => [
						'placeholder' => $titleLabel,
					],
					'wrapper'    => [
						'class' => 'col-md-12',
					],
					'tab'        => $language->name,
				];
				$fields[] = [
					'name'       => 'body_' . $language->code,
					'label'      => $bodyLabel,
					'type'       => ($wysiwygEditor != 'none' && file_exists(resource_path($wysiwygEditorViewPath)))
						? $wysiwygEditor
						: 'textarea',
					'attributes' => [
						'placeholder' => $bodyLabel,
						'id'          => 'description',
						'rows'        => 5,
					],
					'hint'       => trans('admin.body_hint') . ' (' . $language->name . ')',
					'wrapper'    => [
						'class' => 'col-md-12',
					],
					'tab'        => $language->name,
				];
				
				$fields[] = [
					'name'  => 'seo_start_' . $language->code,
					'type'  => 'custom_html',
					'value' => '<hr style="border: 1px dashed #EFEFEF;" class="my-3">',
				];
			}
		}
		
		$fields = self::appendSpacingFormFields($fields, fieldSeparator: 'end');
		
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
