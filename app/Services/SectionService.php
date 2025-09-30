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

use App\Http\Resources\EntityCollection;
use App\Http\Resources\SectionResource;
use App\Models\Scopes\ActiveScope;
use App\Models\Section;
use App\Services\Section\SectionDataTrait;
use App\Services\Section\SectionSettingTrait;
use Illuminate\Http\JsonResponse;
use Throwable;

class SectionService extends BaseService
{
	use SectionDataTrait, SectionSettingTrait;
	
	protected $sectionClass = '\extras\plugins\domainmapping\app\Models\DomainSection';
	
	/**
	 * List sections
	 *
	 * @param array $params
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getSections(array $params = []): JsonResponse
	{
		$countryCode = config('country.code');
		
		// Get all homepage sections
		$cacheId = $countryCode . '.sections';
		$sections = cache()->remember($cacheId, $this->cacheExpiration, function () use ($countryCode) {
			$sections = collect();
			
			// Check if the Domain Mapping plugin is available
			if (config('plugins.domainmapping.installed')) {
				try {
					$sections = $this->sectionClass::query()
						->where('country_code', '=', $countryCode)
						->orderBy('lft')
						->get();
				} catch (Throwable $e) {
				}
			}
			
			// Get the entry from the core
			if ($sections->count() <= 0) {
				$sections = Section::query()->orderBy('lft')->get();
			}
			
			return $sections;
		});
		
		$sectionsList = [];
		if ($sections->count() > 0) {
			/*
			 * Set valid key name (for each Section)
			 * and set the collection key by 'key'
			 * Note: The key name needs to be cleared when the "Domain Mapping Plugin" is installed
			 */
			$sections = $sections->mapWithKeys(function ($item) use ($countryCode) {
				$prefix = strtolower($countryCode) . '_';
				
				$method = $item['name'] ?? '';
				if (str_starts_with($method, $prefix)) {
					$method = str($method)->replaceStart($prefix, '')->toString();
				}
				
				return [$method => $item];
			});
			
			foreach ($sections as $name => $section) {
				$method = str($name)->lower()->camel()->toString();
				
				// Check if key exists
				if (!method_exists($this, $method)) {
					continue;
				}
				
				$belongsTo = $section->belongs_to;
				$parameters = $section->field_values ?? [];
				$cacheExpiration = $parameters['cache_expiration'] ?? $this->cacheExpiration;
				
				/*
				 * Get the section method result & cache it
				 * (since some methods can execute queries)
				 */
				try {
					$cacheId = "{$countryCode}.sections.{$belongsTo}.{$method}";
					$resultData = cache()->remember($cacheId, $cacheExpiration, fn () => $this->{$method}($parameters));
				} catch (Throwable $e) {
					return apiResponse()->error($e->getMessage());
				}
				
				// Update the parameters/options (if parameters-updater exists for this method)
				$settingMethod = $method . 'Settings';
				if (method_exists($this, $settingMethod)) {
					$parameters = $this->{$settingMethod}($parameters);
				}
				
				// Save the section required information
				$sectionArray = [
					'belongs_to' => $belongsTo,
					'name'       => $name,
					'data'       => $resultData,
					'options'    => $parameters,
					'lft'        => $section->lft ?? 0,
				];
				
				// Save in the list
				$sectionsList[$name] = $sectionArray;
			}
		}
		
		$resourceCollection = new EntityCollection(SectionResource::class, $sectionsList, $params);
		
		return apiResponse()->withCollection($resourceCollection);
	}
	
	/**
	 * Get section
	 *
	 * Get category by its unique name or ID.
	 *
	 * @param string $name
	 * @param array $params
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getSectionByKey(string $name, array $params = []): JsonResponse
	{
		$countryCode = config('country.code');
		$isUnactivatedIncluded = getIntAsBoolean($params['unactivatedIncluded'] ?? 0);
		$dataCanBeFetched = getIntAsBoolean($params['fetchData'] ?? 0);
		
		// Get all homepage sections
		$cacheId = $countryCode . '.sections.' . $name . '.includingNonActive.' . (int)$isUnactivatedIncluded;
		$section = cache()->remember($cacheId, $this->cacheExpiration, function () use ($countryCode, $name, $isUnactivatedIncluded) {
			$section = null;
			
			// Check if the Domain Mapping plugin is available
			if (config('plugins.domainmapping.installed')) {
				try {
					$section = $this->sectionClass::query();
					if ($isUnactivatedIncluded) {
						$section->withoutGlobalScopes([ActiveScope::class]);
					}
					$section = $section->where('country_code', '=', $countryCode)->where('name', $name)->first();
				} catch (Throwable $e) {
				}
			}
			
			// Get the entry from the core
			if (empty($section)) {
				$section = Section::query();
				if ($isUnactivatedIncluded) {
					$section->withoutGlobalScopes([ActiveScope::class]);
				}
				$section = $section->where('name', $name)->first();
			}
			
			return $section;
		});
		
		abort_if(empty($section), 404, t('section_not_found'));
		
		// Clear key name
		$name = str_replace(strtolower($countryCode) . '_', '', $section->name);
		$method = str($name)->lower()->camel()->toString();
		
		// Check if key exists
		abort_if(!method_exists($this, $method), 404, t('section_not_found'));
		
		$belongsTo = $section->belongs_to;
		$parameters = $section->field_values ?? [];
		$cacheExpiration = $parameters['cache_expiration'] ?? $this->cacheExpiration;
		
		/*
		 * Get the section method result & cache it
		 * (since some methods can execute queries)
		 */
		$resultData = null;
		try {
			if ($dataCanBeFetched) {
				$cacheId = "{$countryCode}.sections.{$belongsTo}.{$method}";
				$resultData = cache()->remember($cacheId, $cacheExpiration, function () use ($method, $parameters) {
					return $this->{$method}($parameters);
				});
			}
		} catch (Throwable $e) {
			return apiResponse()->error($e->getMessage());
		}
		
		// Update the parameters/options (if parameters-updater exists for this method)
		$settingMethod = $method . 'Settings';
		if (method_exists($this, $settingMethod)) {
			$parameters = $this->{$settingMethod}($parameters);
		}
		
		// Save the section required information
		$sectionArray = [
			'belongsTo' => $belongsTo,
			'name'      => $name,
			'data'      => $resultData,
			'options'   => $parameters,
			'lft'       => $section->lft ?? 0,
		];
		
		$resource = new SectionResource($sectionArray, $params);
		
		return apiResponse()->withResource($resource);
	}
}
