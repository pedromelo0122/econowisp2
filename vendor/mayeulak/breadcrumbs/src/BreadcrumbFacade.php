<?php

namespace Bedigit\Breadcrumbs;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self add(string $title, ?string $url = null)
 * @method static self setHome(string $title, ?string $url = null)
 * @method static self clear()
 * @method static array getItems()
 * @method static bool hasItems()
 * @method static string render(?string $view = null)
 *
 * @see \Bedigit\Breadcrumbs\Breadcrumb
 */
class BreadcrumbFacade extends Facade
{
	protected static function getFacadeAccessor(): string
	{
		return 'breadcrumb';
	}
}
