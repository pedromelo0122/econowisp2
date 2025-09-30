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

namespace App\Services;

use App\Helpers\Common\PaginationHelper;
use App\Http\Resources\EntityCollection;
use App\Http\Resources\SubAdmin1Resource;
use App\Models\SubAdmin1;
use Illuminate\Http\JsonResponse;

class SubAdmin1Service extends BaseService
{
	/**
	 * List admin. divisions (1)
	 *
	 * @param string $countryCode
	 * @param array $params
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getEntries(string $countryCode, array $params = []): JsonResponse
	{
		$locale = config('app.locale');
		$perPage = getNumberOfItemsPerPage('subadmin1', $params['perPage'] ?? null, $this->perPage);
		$page = (int)($params['page'] ?? 1);
		$embed = getCommaSeparatedStrAsArray($params['embed'] ?? []);
		$keyword = $params['keyword'] ?? null;
		$sort = $params['sort'] ?? [];
		
		// Cache ID
		$cacheEmbedId = !empty($embed) ? '.embed.' . implode(',', $embed) : '';
		$cacheFiltersId = '.filters.' . $keyword;
		$cachePageId = '.page.' . $page . '.of.' . $perPage;
		$cacheId = $countryCode . '.admins1.' . $cacheEmbedId . $cacheFiltersId . $cachePageId . '.' . $locale;
		$cacheId = md5($cacheId);
		
		// Cached Query
		$admins1 = cache()->remember($cacheId, $this->cacheExpiration, function () use (
			$perPage, $embed, $countryCode, $keyword, $sort
		) {
			$admins1 = SubAdmin1::query();
			
			if (in_array('country', $embed)) {
				$admins1->with('country');
			}
			
			$admins1->where('country_code', '=', $countryCode);
			if (!empty($keyword)) {
				$admins1->where('name', 'LIKE', '%' . $keyword . '%');
			}
			
			// Sorting
			$admins1 = $this->applySorting($admins1, ['name'], $sort);
			
			$admins1 = $admins1->paginate($perPage);
			
			return PaginationHelper::adjustSides($admins1);
		});
		
		// If the request is made from the app's Web environment,
		// use the Web URL as the pagination's base URL
		$admins1 = setPaginationBaseUrl($admins1);
		
		$resourceCollection = new EntityCollection(SubAdmin1Resource::class, $admins1, $params);
		
		$message = ($admins1->count() <= 0) ? t('no_admin_divisions_found') : null;
		
		return apiResponse()->withCollection($resourceCollection, $message);
	}
	
	/**
	 * Get admin. division (1)
	 *
	 * @param string $code
	 * @param array $params
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getEntry(string $code, array $params = []): JsonResponse
	{
		$embed = getCommaSeparatedStrAsArray($params['embed'] ?? []);
		
		// Cache ID
		$cacheEmbedId = !empty($embed) ? '.embed.' . implode(',', $embed) : '';
		$cacheId = 'admin1.' . $code . $cacheEmbedId;
		
		// Cached Query
		$admin1 = cache()->remember($cacheId, $this->cacheExpiration, function () use ($code, $embed) {
			$admin1 = SubAdmin1::query()->where('code', '=', $code);
			
			if (in_array('country', $embed)) {
				$admin1->with('country');
			}
			
			return $admin1->first();
		});
		
		abort_if(empty($admin1), 404, t('admin_division_not_found'));
		
		$resource = new SubAdmin1Resource($admin1, $params);
		
		return apiResponse()->withResource($resource);
	}
}
