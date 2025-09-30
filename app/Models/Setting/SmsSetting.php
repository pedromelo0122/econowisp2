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
 * settings.sms.option
 */

class SmsSetting
{
	public static function getFieldValues($value, $disk)
	{
		$value = is_array($value) ? $value : [];
		
		$enablePhoneAsAuthField = $value['enable_phone_as_auth_field'] ?? '0';
		$phoneVerification = $value['phone_verification'] ?? '0';
		$smsConfirmationReceiving = $value['confirmation'] ?? '0';
		$smsMessageReceiving = $value['messenger_notifications'] ?? '0';
		
		$smsSendingIsRequired = (
			$enablePhoneAsAuthField == '1'
			&& ($phoneVerification == '1' || $smsConfirmationReceiving == '1' || $smsMessageReceiving == '1')
		);
		$phoneValidator = $smsSendingIsRequired ? 'isPossibleMobileNumber' : 'isPossiblePhoneNumber';
		
		$defaultValue = [
			'enable_phone_as_auth_field' => '0',
			'default_auth_field'         => 'email',
			'phone_of_countries'         => 'local',
			'phone_validator'            => $phoneValidator,
			'phone_placeholder_type'     => 'auto-0',
			'vonage_key'                 => env('VONAGE_KEY', ''),
			'vonage_secret'              => env('VONAGE_SECRET', ''),
			'vonage_application_id'      => env('VONAGE_APPLICATION_ID', ''),
			'vonage_from'                => env('VONAGE_SMS_FROM', ''),
			'twilio_username'            => env('TWILIO_USERNAME', ''),
			'twilio_password'            => env('TWILIO_PASSWORD', ''),
			'twilio_auth_token'          => env('TWILIO_AUTH_TOKEN', ''),
			'twilio_account_sid'         => env('TWILIO_ACCOUNT_SID', ''),
			'twilio_from'                => env('TWILIO_FROM', ''),
			'twilio_alpha_sender'        => env('TWILIO_ALPHA_SENDER', ''),
			'twilio_sms_service_sid'     => env('TWILIO_SMS_SERVICE_SID', ''),
			'twilio_debug_to'            => env('TWILIO_DEBUG_TO', ''),
			'phone_verification'         => '1',
		];
		
		return array_merge($defaultValue, $value);
	}
	
	public static function setFieldValues($value, $setting)
	{
		return $value;
	}
	
	public static function getFields($diskName): array
	{
		// Get Drivers List
		$smsDrivers = (array)config('larapen.options.sms');
		
		// Get the drivers selectors list as JS objects
		$smsDriversSelectorsJson = collect($smsDrivers)
			->keys()
			->mapWithKeys(fn ($item) => [$item => '.' . $item])
			->toJson();
		
		$fields = [];
		
		$fields[] = [
			'name'    => 'enable_phone_as_auth_field',
			'label'   => trans('admin.enable_phone_as_auth_field_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.card_light_inverse', [
				'content' => trans('admin.enable_phone_as_auth_field_hint', [
					'phone_verification_label' => trans('admin.phone_verification_label'),
				]),
			]),
			'wrapper' => [
				'class' => 'col-md-6 mt-3',
			],
		];
		$fields[] = [
			'name'    => 'default_auth_field',
			'label'   => trans('admin.default_auth_field_label'),
			'type'    => 'select2_from_array',
			'options' => [
				'email' => t('email_address'),
				'phone' => t('phone_number'),
			],
			'default' => 'email',
			'hint'    => trans('admin.card_light_inverse', [
				'content' => trans('admin.default_auth_field_hint', [
					'enable_phone_as_auth_field_label' => trans('admin.enable_phone_as_auth_field_label'),
					'email'                            => t('email_address'),
					'phone'                            => t('phone_number'),
				]),
			]),
			'wrapper' => [
				'class' => 'col-md-6 auth-field-el',
			],
			'newline' => true,
		];
		
		$phoneOfCountriesOptions = [
			'local'     => trans('admin.phone_of_countries_op_1'),
			'activated' => trans('admin.phone_of_countries_op_2'),
			'all'       => trans('admin.phone_of_countries_op_3'),
		];
		$phoneValidatorOptions = [
			'none'                   => trans('admin.phone_validator_op_0'),
			'isValidMobileNumber'    => trans('admin.phone_validator_op_1'),
			'isPossibleMobileNumber' => trans('admin.phone_validator_op_2'),
			'isValidPhoneNumber'     => trans('admin.phone_validator_op_3'),
			'isPossiblePhoneNumber'  => trans('admin.phone_validator_op_4'),
		];
		$fields[] = [
			'name'    => 'phone_of_countries',
			'label'   => trans('admin.phone_of_countries_label'),
			'type'    => 'select2_from_array',
			'options' => $phoneOfCountriesOptions,
			'hint'    => trans('admin.card_light_inverse', [
				'content' => trans('admin.phone_of_countries_hint', $phoneOfCountriesOptions),
			]),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'phone_validator',
			'label'   => trans('admin.phone_validator_label'),
			'type'    => 'select2_from_array',
			'options' => $phoneValidatorOptions,
			'hint'    => trans('admin.card_light_inverse', [
				'content' => trans('admin.phone_validator_hint', array_merge($phoneValidatorOptions, [
					'enable_phone_as_auth_field_label' => trans('admin.enable_phone_as_auth_field_label'),
				])),
			]),
			'wrapper' => [
				'class' => 'col-md-6',
			],
		];
		$fields[] = [
			'name'    => 'phone_placeholder_type',
			'label'   => trans('admin.phone_placeholder_type_label'),
			'type'    => 'select2_from_array',
			'options' => getPhonePlaceholderTypes(),
			'hint'    => trans('admin.card_light_inverse', [
				'content' => trans('admin.phone_placeholder_type_hint'),
			]),
			'wrapper' => [
				'class' => 'col-md-6 mt-3',
			],
		];
		
		$fields[] = [
			'name'    => 'default_auth_field_sep',
			'type'    => 'custom_html',
			'value'   => '<hr style="border: 1px dashed #EFEFEF;" class="my-3">',
			'wrapper' => [
				'class' => 'col-12',
			],
		];
		
		// driver
		$fields[] = [
			'name'    => 'driver',
			'label'   => trans('admin.SMS Driver'),
			'type'    => 'select2_from_array',
			'options' => $smsDrivers,
			'wrapper' => [
				'class' => 'col-md-6',
			],
			'newline' => true,
		];
		
		// vonage
		if (array_key_exists('vonage', $smsDrivers)) {
			$fields[] = [
				'name'    => 'driver_vonage_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_vonage_title'),
				'wrapper' => [
					'class' => 'col-md-12 vonage',
				],
			];
			$fields[] = [
				'name'    => 'driver_vonage_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_vonage_info'),
				'wrapper' => [
					'class' => 'col-md-12 vonage',
				],
			];
			$fields[] = [
				'name'     => 'vonage_key',
				'label'    => trans('admin.Vonage Key'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 vonage',
				],
			];
			$fields[] = [
				'name'     => 'vonage_secret',
				'label'    => trans('admin.Vonage Secret'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 vonage',
				],
			];
			$fields[] = [
				'name'     => 'vonage_application_id',
				'label'    => trans('admin.vonage_application_id'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 vonage',
				],
			];
			$fields[] = [
				'name'     => 'vonage_from',
				'label'    => trans('admin.Vonage From'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 vonage',
				],
			];
		}
		
		// twilio
		if (array_key_exists('twilio', $smsDrivers)) {
			$fields[] = [
				'name'    => 'driver_twilio_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_twilio_title'),
				'wrapper' => [
					'class' => 'col-md-12 twilio',
				],
			];
			$fields[] = [
				'name'    => 'driver_twilio_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_twilio_info'),
				'wrapper' => [
					'class' => 'col-md-12 twilio',
				],
			];
			$fields[] = [
				'name'     => 'twilio_username',
				'label'    => trans('admin.twilio_username_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.twilio_username_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 twilio',
				],
			];
			$fields[] = [
				'name'     => 'twilio_password',
				'label'    => trans('admin.twilio_password_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.twilio_password_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 twilio',
				],
			];
			$fields[] = [
				'name'     => 'twilio_account_sid',
				'label'    => trans('admin.twilio_account_sid_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.twilio_account_sid_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 twilio',
				],
			];
			$fields[] = [
				'name'     => 'twilio_auth_token',
				'label'    => trans('admin.twilio_auth_token_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.twilio_auth_token_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 twilio',
				],
			];
			$fields[] = [
				'name'     => 'twilio_from',
				'label'    => trans('admin.twilio_from_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.twilio_from_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 twilio',
				],
			];
			$fields[] = [
				'name'    => 'twilio_alpha_sender',
				'label'   => trans('admin.twilio_alpha_sender_label'),
				'type'    => 'text',
				'hint'    => trans('admin.twilio_alpha_sender_hint'),
				'wrapper' => [
					'class' => 'col-md-6 twilio',
				],
			];
			$fields[] = [
				'name'     => 'twilio_sms_service_sid',
				'label'    => trans('admin.twilio_sms_service_sid_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.twilio_sms_service_sid_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 twilio',
				],
			];
			$fields[] = [
				'name'    => 'twilio_debug_to',
				'label'   => trans('admin.twilio_debug_to_label'),
				'type'    => 'text',
				'hint'    => trans('admin.twilio_debug_to_hint'),
				'wrapper' => [
					'class' => 'col-md-6 twilio',
				],
			];
		}
		
		$fields[] = [
			'name'  => 'driver_test_title',
			'type'  => 'custom_html',
			'value' => trans('admin.driver_test_title'),
		];
		$fields[] = [
			'name'  => 'driver_test_info',
			'type'  => 'custom_html',
			'value' => trans('admin.card_light', [
				'content' => trans('admin.sms_driver_test_info', ['smsTo' => trans('admin.sms_to_label')]),
			]),
		];
		$fields[] = [
			'name'    => 'driver_test',
			'label'   => trans('admin.driver_test_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.sms_driver_test_hint'),
			'wrapper' => [
				'class' => 'col-md-6 mt-2',
			],
		];
		$fields[] = [
			'name'     => 'sms_to',
			'label'    => trans('admin.sms_to_label'),
			'type'     => 'tel',
			'default'  => config('settings.app.phone_number'),
			'required' => true,
			'hint'     => trans('admin.sms_to_hint', ['option' => trans('admin.driver_test_label')]),
			'wrapper'  => [
				'class' => 'col-md-6 driver-test',
			],
		];
		
		if (config('settings.optimization.queue_driver') != 'sync') {
			$fields[] = [
				'name'  => 'queue_notifications',
				'type'  => 'custom_html',
				'value' => trans('admin.card_light_warning', [
					'content' => trans('admin.queue_notifications', ['queueOptionUrl' => urlGen()->adminUrl('settings/find/optimization')]),
				]),
			];
		}
		
		$fields[] = [
			'name'  => 'sms_notification_types_title',
			'type'  => 'custom_html',
			'value' => trans('admin.sms_notification_types_title'),
		];
		$fields[] = [
			'name'  => 'phone_verification',
			'label' => trans('admin.phone_verification_label'),
			'type'  => 'checkbox_switch',
			'hint'  => trans('admin.phone_verification_hint', [
					'email_verification_label' => trans('admin.email_verification_label'),
				]) . '<br>' . trans('admin.sms_sending_requirements'),
		];
		$fields[] = [
			'name'  => 'confirmation',
			'label' => trans('admin.settings_sms_confirmation_label'),
			'type'  => 'checkbox_switch',
			'hint'  => trans('admin.settings_sms_confirmation_hint') . '<br>' . trans('admin.sms_sending_requirements'),
		];
		$fields[] = [
			'name'  => 'messenger_notifications',
			'label' => trans('admin.messenger_notifications_label'),
			'type'  => 'checkbox_switch',
			'hint'  => trans('admin.messenger_notifications_hint') . '<br>' . trans('admin.sms_sending_requirements'),
		];
		
		return addOptionsGroupJavaScript(__NAMESPACE__, __CLASS__, $fields, [
			'smsDriversSelectorsJson' => $smsDriversSelectorsJson,
		]);
	}
}
