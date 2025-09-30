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

use App\Helpers\Common\CustomCache;
use App\Helpers\Common\Files\Thumbnail;
use App\Helpers\Common\Response\Ajax;
use App\Helpers\Common\Response\Api;
use App\Helpers\Common\SystemLocale;
use App\Helpers\Common\UrlQuery;
use App\Helpers\Services\ThumbnailParams;
use App\Helpers\Services\ThumbnailService;
use App\Helpers\Services\UrlGen;
use Illuminate\Support\ViewErrorBag;

/**
 * Get the custom cache instance.
 *
 * @param array|null $tags
 * @return \App\Helpers\Common\CustomCache
 */
function customCache(?array $tags = null): CustomCache
{
	return new CustomCache($tags);
}

/**
 * SystemLocale Object
 * Note: This is different to the app()->setLocale() and app()->getLocale() that are related to the app's locale
 *
 * @return \App\Helpers\Common\SystemLocale
 */
function systemLocale(): SystemLocale
{
	return new SystemLocale();
}

/**
 * API Response Object
 *
 * @return \App\Helpers\Common\Response\Api
 */
function apiResponse(): Api
{
	return new Api();
}

/**
 * AJAX Response Object
 *
 * @return \App\Helpers\Common\Response\Ajax
 */
function ajaxResponse(): Ajax
{
	return new Ajax();
}

/**
 * Build a thumbnail params & URL
 *
 * @param string|null $filePath
 * @param string|bool|null $filePathFallback
 * @return \App\Helpers\Services\ThumbnailParams
 */
function thumbParam(?string $filePath, string|null|bool $filePathFallback = null): ThumbnailParams
{
	return new ThumbnailParams($filePath, $filePathFallback);
}

/**
 * Create a thumbnail image & URL
 *
 * @param string|null $filePath
 * @param string|bool|null $filePathFallback
 * @return \App\Helpers\Services\ThumbnailService
 */
function thumbService(?string $filePath, string|null|bool $filePathFallback = null): ThumbnailService
{
	return new ThumbnailService($filePath, $filePathFallback);
}

/**
 * Create a thumbnail image
 *
 * @param string|null $filePath
 * @param string|bool|null $filePathFallback
 * @return \App\Helpers\Common\Files\Thumbnail
 */
function thumbImage(?string $filePath, string|null|bool $filePathFallback = null): Thumbnail
{
	return new Thumbnail($filePath, $filePathFallback);
}

/**
 * @param string|null $url
 * @return \App\Helpers\Common\UrlQuery
 */
function urlQuery(?string $url = null): UrlQuery
{
	return new UrlQuery($url);
}

/**
 * @return \App\Helpers\Services\UrlGen
 */
function urlGen(): UrlGen
{
	return new UrlGen();
}

/**
 * Get empty Laravel view errors collection
 *
 * @return \Illuminate\Support\ViewErrorBag
 */
function getEmptyViewErrors(): ViewErrorBag
{
	return new ViewErrorBag;
}
