<?php

namespace Bedigit\Breadcrumbs;

use Illuminate\Support\ServiceProvider;

class BreadcrumbServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../config/breadcrumbs.php' => config_path('breadcrumbs.php'),
		], 'breadcrumbs-config');
		
		$this->publishes([
			__DIR__ . '/../resources/views' => resource_path('views/vendor/breadcrumbs'),
		], 'breadcrumbs-views');
		
		$this->publishes([
			__DIR__ . '/../resources/css' => public_path('vendor/breadcrumbs'),
		], 'breadcrumbs-assets');
		
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'breadcrumbs');
		
		$this->app->singleton('breadcrumb', function () {
			return new Breadcrumb();
		});
	}
	
	public function register()
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/breadcrumbs.php', 'breadcrumbs');
	}
}
