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
 * settings.mail.option
 */

class MailSetting
{
	public static function getFieldValues($value, $disk)
	{
		$value = is_array($value) ? $value : [];
		
		$defaultValue = [
			'sendmail_path' => config('mail.mailers.sendmail.path'),
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
		$mailDrivers = (array)config('larapen.options.mail');
		
		// Get the drivers selectors list as JS objects
		$mailDriversSelectorsJson = collect($mailDrivers)
			->keys()
			->mapWithKeys(fn ($item) => [$item => '.' . $item])
			->toJson();
		
		$fields = [];
		
		$fields[] = [
			'name'    => 'driver',
			'label'   => trans('admin.mail_driver_label'),
			'type'    => 'select2_from_array',
			'options' => $mailDrivers,
			'wrapper' => [
				'class' => 'col-md-6',
			],
			'newline' => true,
		];
		
		// sendmail
		if (array_key_exists('sendmail', $mailDrivers)) {
			$fields[] = [
				'name'    => 'driver_sendmail_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_sendmail_title'),
				'wrapper' => [
					'class' => 'col-md-12 sendmail',
				],
			];
			$fields[] = [
				'name'    => 'driver_sendmail_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_sendmail_info'),
				'wrapper' => [
					'class' => 'col-md-12 sendmail',
				],
			];
			$fields[] = [
				'name'    => 'sendmail_path',
				'label'   => trans('admin.sendmail_path_label'),
				'type'    => 'text',
				'hint'    => trans('admin.sendmail_path_hint'),
				'wrapper' => [
					'class' => 'col-md-12 sendmail',
				],
			];
			$fields[] = [
				'name'     => 'sendmail_email_sender',
				'label'    => trans('admin.mail_email_sender_label'),
				'type'     => 'email',
				'default'  => config('settings.app.email'),
				'required' => true,
				'hint'     => trans('admin.mail_email_sender_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 sendmail',
				],
			];
		}
		
		// smtp
		if (array_key_exists('smtp', $mailDrivers)) {
			$fields[] = [
				'name'    => 'driver_smtp_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_smtp_title'),
				'wrapper' => [
					'class' => 'col-md-12 smtp',
				],
			];
			$fields[] = [
				'name'    => 'driver_smtp_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_smtp_info'),
				'wrapper' => [
					'class' => 'col-md-12 smtp',
				],
			];
			$fields[] = [
				'name'     => 'smtp_host',
				'label'    => trans('admin.mail_smtp_host_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_host_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 smtp',
				],
			];
			$fields[] = [
				'name'     => 'smtp_port',
				'label'    => trans('admin.mail_smtp_port_label'),
				'type'     => 'number',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_port_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 smtp',
				],
			];
			$fields[] = [
				'name'    => 'smtp_username',
				'label'   => trans('admin.mail_smtp_username_label'),
				'type'    => 'text',
				'hint'    => trans('admin.mail_smtp_username_hint'),
				'wrapper' => [
					'class' => 'col-md-6 smtp',
				],
			];
			$fields[] = [
				'name'    => 'smtp_password',
				'label'   => trans('admin.mail_smtp_password_label'),
				'type'    => 'text',
				'hint'    => trans('admin.mail_smtp_password_hint'),
				'wrapper' => [
					'class' => 'col-md-6 smtp',
				],
			];
			$fields[] = [
				'name'    => 'smtp_encryption',
				'label'   => trans('admin.mail_smtp_encryption_label'),
				'type'    => 'text',
				'hint'    => trans('admin.mail_smtp_encryption_hint'),
				'wrapper' => [
					'class' => 'col-md-6 smtp',
				],
			];
			$fields[] = [
				'name'     => 'smtp_email_sender',
				'label'    => trans('admin.mail_email_sender_label'),
				'type'     => 'email',
				'default'  => config('settings.app.email'),
				'required' => true,
				'hint'     => trans('admin.mail_email_sender_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 smtp',
				],
			];
			$fields[] = [
				'name'    => 'verify_peer',
				'label'   => trans('admin.verify_peer_label'),
				'type'    => 'checkbox_switch',
				'hint'    => trans('admin.verify_peer_hint'),
				'wrapper' => [
					'class' => 'col-md-6 mt-2',
				],
			];
		}
		
		// mailgun
		if (array_key_exists('mailgun', $mailDrivers)) {
			$fields[] = [
				'name'    => 'driver_mailgun_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_mailgun_title'),
				'wrapper' => [
					'class' => 'col-md-12 mailgun',
				],
			];
			$fields[] = [
				'name'    => 'driver_mailgun_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_mailgun_info'),
				'wrapper' => [
					'class' => 'col-md-12 mailgun',
				],
			];
			$fields[] = [
				'name'     => 'mailgun_domain',
				'label'    => trans('admin.mail_mailgun_domain_label'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 mailgun',
				],
			];
			$fields[] = [
				'name'     => 'mailgun_secret',
				'label'    => trans('admin.mail_mailgun_secret_label'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 mailgun',
				],
			];
			$fields[] = [
				'name'     => 'mailgun_endpoint',
				'label'    => trans('admin.mail_mailgun_endpoint_label'),
				'type'     => 'text',
				'default'  => 'api.mailgun.net',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 mailgun',
				],
			];
			$fields[] = [
				'name'     => 'mailgun_host',
				'label'    => trans('admin.mail_smtp_host_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_host_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 mailgun',
				],
			];
			$fields[] = [
				'name'     => 'mailgun_port',
				'label'    => trans('admin.mail_smtp_port_label'),
				'type'     => 'number',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_port_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 mailgun',
				],
			];
			$fields[] = [
				'name'     => 'mailgun_username',
				'label'    => trans('admin.mail_smtp_username_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_username_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 mailgun',
				],
			];
			$fields[] = [
				'name'     => 'mailgun_password',
				'label'    => trans('admin.mail_smtp_password_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_password_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 mailgun',
				],
			];
			$fields[] = [
				'name'     => 'mailgun_encryption',
				'label'    => trans('admin.mail_smtp_encryption_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_encryption_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 mailgun',
				],
			];
			$fields[] = [
				'name'     => 'mailgun_email_sender',
				'label'    => trans('admin.mail_email_sender_label'),
				'type'     => 'email',
				'default'  => config('settings.app.email'),
				'required' => true,
				'hint'     => trans('admin.mail_email_sender_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 mailgun',
				],
			];
		}
		
		// postmark
		if (array_key_exists('postmark', $mailDrivers)) {
			$fields[] = [
				'name'    => 'driver_postmark_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_postmark_title'),
				'wrapper' => [
					'class' => 'col-md-12 postmark',
				],
			];
			$fields[] = [
				'name'    => 'driver_postmark_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_postmark_info'),
				'wrapper' => [
					'class' => 'col-md-12 postmark',
				],
			];
			$fields[] = [
				'name'     => 'postmark_token',
				'label'    => trans('admin.mail_postmark_token_label'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 postmark',
				],
			];
			$fields[] = [
				'name'     => 'postmark_host',
				'label'    => trans('admin.mail_smtp_host_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_host_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 postmark',
				],
			];
			$fields[] = [
				'name'     => 'postmark_port',
				'label'    => trans('admin.mail_smtp_port_label'),
				'type'     => 'number',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_port_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 postmark',
				],
			];
			$fields[] = [
				'name'     => 'postmark_username',
				'label'    => trans('admin.mail_smtp_username_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_username_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 postmark',
				],
			];
			$fields[] = [
				'name'     => 'postmark_password',
				'label'    => trans('admin.mail_smtp_password_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_password_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 postmark',
				],
			];
			$fields[] = [
				'name'     => 'postmark_encryption',
				'label'    => trans('admin.mail_smtp_encryption_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_encryption_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 postmark',
				],
			];
			$fields[] = [
				'name'     => 'postmark_email_sender',
				'label'    => trans('admin.mail_email_sender_label'),
				'type'     => 'email',
				'default'  => config('settings.app.email'),
				'required' => true,
				'hint'     => trans('admin.mail_email_sender_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 postmark',
				],
			];
		}
		
		// ses
		if (array_key_exists('ses', $mailDrivers)) {
			$fields[] = [
				'name'    => 'driver_ses_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_ses_title'),
				'wrapper' => [
					'class' => 'col-md-12 ses',
				],
			];
			$fields[] = [
				'name'    => 'driver_ses_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_ses_info'),
				'wrapper' => [
					'class' => 'col-md-12 ses',
				],
			];
			$fields[] = [
				'name'     => 'ses_key',
				'label'    => trans('admin.mail_ses_key_label'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 ses',
				],
			];
			$fields[] = [
				'name'     => 'ses_secret',
				'label'    => trans('admin.mail_ses_secret_label'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 ses',
				],
			];
			$fields[] = [
				'name'     => 'ses_region',
				'label'    => trans('admin.mail_ses_region_label'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 ses',
				],
			];
			$fields[] = [
				'name'     => 'ses_token',
				'label'    => trans('admin.mail_ses_token_label'),
				'type'     => 'text',
				'required' => false,
				'hint'     => trans('admin.mail_ses_token_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 ses',
				],
			];
			$fields[] = [
				'name'     => 'ses_host',
				'label'    => trans('admin.mail_smtp_host_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_host_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 ses',
				],
			];
			$fields[] = [
				'name'     => 'ses_port',
				'label'    => trans('admin.mail_smtp_port_label'),
				'type'     => 'number',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_port_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 ses',
				],
			];
			$fields[] = [
				'name'     => 'ses_username',
				'label'    => trans('admin.mail_smtp_username_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_username_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 ses',
				],
			];
			$fields[] = [
				'name'     => 'ses_password',
				'label'    => trans('admin.mail_smtp_password_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_password_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 ses',
				],
			];
			$fields[] = [
				'name'     => 'ses_encryption',
				'label'    => trans('admin.mail_smtp_encryption_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_encryption_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 ses',
				],
			];
			$fields[] = [
				'name'     => 'ses_email_sender',
				'label'    => trans('admin.mail_email_sender_label'),
				'type'     => 'email',
				'default'  => config('settings.app.email'),
				'required' => true,
				'hint'     => trans('admin.mail_email_sender_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 ses',
				],
			];
		}
		
		// sparkpost
		if (array_key_exists('sparkpost', $mailDrivers)) {
			$fields[] = [
				'name'    => 'driver_sparkpost_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_sparkpost_title'),
				'wrapper' => [
					'class' => 'col-md-12 sparkpost',
				],
			];
			$fields[] = [
				'name'    => 'driver_sparkpost_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_sparkpost_info'),
				'wrapper' => [
					'class' => 'col-md-12 sparkpost',
				],
			];
			$fields[] = [
				'name'     => 'sparkpost_secret',
				'label'    => trans('admin.mail_sparkpost_secret_label'),
				'type'     => 'text',
				'required' => true,
				'wrapper'  => [
					'class' => 'col-md-6 sparkpost',
				],
			];
			$fields[] = [
				'name'     => 'sparkpost_host',
				'label'    => trans('admin.mail_smtp_host_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_host_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 sparkpost',
				],
			];
			$fields[] = [
				'name'     => 'sparkpost_port',
				'label'    => trans('admin.mail_smtp_port_label'),
				'type'     => 'number',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_port_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 sparkpost',
				],
			];
			$fields[] = [
				'name'     => 'sparkpost_username',
				'label'    => trans('admin.mail_smtp_username_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_username_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 sparkpost',
				],
			];
			$fields[] = [
				'name'     => 'sparkpost_password',
				'label'    => trans('admin.mail_smtp_password_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_password_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 sparkpost',
				],
			];
			$fields[] = [
				'name'     => 'sparkpost_encryption',
				'label'    => trans('admin.mail_smtp_encryption_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_smtp_encryption_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 sparkpost',
				],
			];
			$fields[] = [
				'name'     => 'sparkpost_email_sender',
				'label'    => trans('admin.mail_email_sender_label'),
				'type'     => 'email',
				'default'  => config('settings.app.email'),
				'required' => true,
				'hint'     => trans('admin.mail_email_sender_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 sparkpost',
				],
			];
		}
		
		// resend
		if (array_key_exists('resend', $mailDrivers)) {
			$fields[] = [
				'name'    => 'driver_resend_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_resend_title'),
				'wrapper' => [
					'class' => 'col-md-12 resend',
				],
			];
			$fields[] = [
				'name'    => 'driver_resend_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_resend_info'),
				'wrapper' => [
					'class' => 'col-md-12 resend',
				],
			];
			$fields[] = [
				'name'     => 'resend_api_key',
				'label'    => trans('admin.mail_resend_api_key_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_resend_api_key_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 resend',
				],
			];
			$fields[] = [
				'name'     => 'resend_email_sender',
				'label'    => trans('admin.mail_email_sender_label'),
				'type'     => 'email',
				'default'  => config('settings.app.email'),
				'required' => true,
				'hint'     => trans('admin.mail_email_sender_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 resend',
				],
			];
		}
		
		// mailersend
		if (array_key_exists('mailersend', $mailDrivers)) {
			$fields[] = [
				'name'    => 'driver_mailersend_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_mailersend_title'),
				'wrapper' => [
					'class' => 'col-md-12 mailersend',
				],
			];
			$fields[] = [
				'name'    => 'driver_mailersend_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_mailersend_info'),
				'wrapper' => [
					'class' => 'col-md-12 mailersend',
				],
			];
			$fields[] = [
				'name'     => 'mailersend_api_key',
				'label'    => trans('admin.mail_mailersend_api_key_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_mailersend_api_key_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 mailersend',
				],
			];
			$fields[] = [
				'name'     => 'mailersend_email_sender',
				'label'    => trans('admin.mail_email_sender_label'),
				'type'     => 'email',
				'default'  => config('settings.app.email'),
				'required' => true,
				'hint'     => trans('admin.mail_email_sender_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 mailersend',
				],
			];
		}
		
		// brevo
		if (array_key_exists('brevo', $mailDrivers)) {
			$fields[] = [
				'name'    => 'driver_brevo_title',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_brevo_title'),
				'wrapper' => [
					'class' => 'col-md-12 brevo',
				],
			];
			$fields[] = [
				'name'    => 'driver_brevo_info',
				'type'    => 'custom_html',
				'value'   => trans('admin.driver_brevo_info'),
				'wrapper' => [
					'class' => 'col-md-12 brevo',
				],
			];
			$fields[] = [
				'name'     => 'brevo_api_key',
				'label'    => trans('admin.mail_brevo_api_key_label'),
				'type'     => 'text',
				'required' => true,
				'hint'     => trans('admin.mail_brevo_api_key_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 brevo',
				],
			];
			$fields[] = [
				'name'     => 'brevo_email_sender',
				'label'    => trans('admin.mail_email_sender_label'),
				'type'     => 'email',
				'default'  => config('settings.app.email'),
				'required' => true,
				'hint'     => trans('admin.mail_email_sender_hint'),
				'wrapper'  => [
					'class' => 'col-md-6 brevo',
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
				'content' => trans('admin.mail_driver_test_info', ['alwaysTo' => trans('admin.email_always_to_label')]),
			]),
		];
		$fields[] = [
			'name'    => 'driver_test',
			'label'   => trans('admin.driver_test_label'),
			'type'    => 'checkbox_switch',
			'hint'    => trans('admin.mail_driver_test_hint'),
			'wrapper' => [
				'class' => 'col-md-6 mt-2',
			],
		];
		$fields[] = [
			'name'     => 'email_always_to',
			'label'    => trans('admin.email_always_to_label'),
			'type'     => 'email',
			'default'  => config('settings.app.email'),
			'required' => true,
			'hint'     => trans('admin.email_always_to_hint', ['option' => trans('admin.driver_test_label')]),
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
			'name'  => 'mail_notification_types_title',
			'type'  => 'custom_html',
			'value' => trans('admin.mail_notification_types_title'),
		];
		$fields[] = [
			'name'  => 'email_verification',
			'label' => trans('admin.email_verification_label'),
			'type'  => 'checkbox_switch',
			'hint'  => trans('admin.email_verification_hint'),
		];
		$fields[] = [
			'name'  => 'confirmation',
			'label' => trans('admin.settings_mail_confirmation_label'),
			'type'  => 'checkbox_switch',
			'hint'  => trans('admin.settings_mail_confirmation_hint'),
		];
		$fields[] = [
			'name'  => 'admin_notification',
			'label' => trans('admin.settings_mail_admin_notification_label'),
			'type'  => 'checkbox_switch',
			'hint'  => trans('admin.settings_mail_admin_notification_hint'),
		];
		$fields[] = [
			'name'  => 'payment_notification',
			'label' => trans('admin.settings_mail_payment_notification_label'),
			'type'  => 'checkbox_switch',
			'hint'  => trans('admin.settings_mail_payment_notification_hint'),
		];
		
		return addOptionsGroupJavaScript(__NAMESPACE__, __CLASS__, $fields, [
			'mailDriversSelectorsJson' => $mailDriversSelectorsJson,
		]);
	}
}
