<?php

namespace extras\plugins\reviews;

use App\Models\Setting;
use extras\plugins\reviews\database\MigrationsTrait;
use Illuminate\Support\Facades\Schema;
use Throwable;

class Reviews
{
	use MigrationsTrait;
	
	/**
	 * @return string
	 */
	public static function getAdminMenu(): string
	{
		$out = '<li class="sidebar-item">';
		$out .= '<a href="' . urlGen()->adminUrl('reviews') . '" class="sidebar-link">';
		$out .= '<i data-feather="message-square" class="feather-icon"></i> ';
		$out .= '<span class="hide-menu">' . trans('reviews::messages.Reviews') . '</span>';
		$out .= '</a>';
		$out .= '</li>';
		
		return $out;
	}
	
	/**
	 * @return array
	 */
	public static function getOptions(): array
	{
		$options = [];
		$options[] = (object)[
			'name'     => trans('reviews::messages.Reviews'),
			'url'      => urlGen()->adminUrl('reviews'),
			'btnClass' => 'btn-primary',
			'iClass'   => 'fa-regular fa-comment-dots',
		];
		$setting = Setting::active()->where('name', 'reviews')->first();
		if (!empty($setting)) {
			$options[] = (object)[
				'name'     => mb_ucfirst(trans('admin.settings')),
				'url'      => urlGen()->adminUrl('settings/' . $setting->id . '/edit'),
				'btnClass' => 'btn-info',
			];
		}
		
		return $options;
	}
	
	/**
	 * @return bool
	 */
	public static function isPreInstalled(): bool
	{
		if (
			Schema::hasTable('reviews')
			&& Schema::hasColumn('posts', 'rating_cache')
			&& Schema::hasColumn('posts', 'rating_count')
		) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public static function installed(): bool
	{
		$cacheExpiration = 86400; // Cache for 1 day (60 * 60 * 24)
		
		return cache()->remember('plugins.reviews.installed', $cacheExpiration, function () {
			$setting = Setting::active()->where('name', 'reviews')->first();
			if (!empty($setting)) {
				if (
					Schema::hasTable('reviews')
					&& Schema::hasColumn('posts', 'rating_cache')
					&& Schema::hasColumn('posts', 'rating_count')
				) {
					return true;
				}
			}
			
			return false;
		});
	}
	
	/**
	 * @return bool
	 */
	public static function install(): bool
	{
		// Remove the plugin entry
		if (!self::isPreInstalled()) {
			self::uninstall();
		}
		
		try {
			// Run the plugin's install migration
			if (!self::isPreInstalled()) {
				self::migrationsInstall();
			}
			
			// Create plugin setting
			$pluginSetting = [
				'name'        => 'reviews',
				'label'       => 'Reviews',
				'description' => 'Reviews System',
			];
			
			return createPluginSetting($pluginSetting);
		} catch (Throwable $e) {
			notification($e->getMessage(), 'error');
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public static function uninstall(): bool
	{
		try {
			cache()->forget('plugins.reviews.installed');
		} catch (Throwable $e) {
		}
		
		try {
			// Run the plugin's uninstall migration
			self::migrationsUninstall();
			
			// Remove the plugin setting
			dropPluginSetting('reviews');
			
			return true;
		} catch (Throwable $e) {
			notification($e->getMessage(), 'error');
		}
		
		return false;
	}
}
