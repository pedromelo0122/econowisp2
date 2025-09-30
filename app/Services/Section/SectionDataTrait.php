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

namespace App\Services\Section;

use App\Helpers\Services\Search\PostQueries;
use App\Models\Advertising;
use App\Models\Category;
use App\Models\City;
use App\Models\Post;
use App\Models\User;

trait SectionDataTrait
{
	/**
	 * Get search form (Always in Top)
	 *
	 * @param array|null $values
	 * @return array
	 */
	protected function searchForm(?array $values = []): array
	{
		return [];
	}
	
	/**
	 * Get locations & SVG map
	 *
	 * @param array|null $values
	 * @return array
	 */
	protected function locations(?array $values = []): array
	{
		$data = [];
		
		$cacheExpiration = (int)($values['cache_expiration'] ?? 0);
		$maxItems = (int)($values['max_items'] ?? 14);
		
		// Get cities
		$isPostsCountEnabled = (config('settings.listings_list.count_cities_listings') == '1');
		$cacheId = config('country.code') . 'section.locations.cities.withCountPosts.' . (int)$isPostsCountEnabled;
		$cities = cache()->remember($cacheId, $cacheExpiration, function () use ($maxItems, $isPostsCountEnabled) {
			return City::query()
				->inCountry()
				->when($isPostsCountEnabled, fn ($query) => $query->withCount('posts'))
				->take($maxItems)
				->orderByDesc('population')
				->orderBy('name')
				->get();
		});
		
		$cities = collect($cities->toArray());
		
		// Add "More Cities" link
		$adminType = config('country.admin_type', 0);
		$adminCodeCol = 'subadmin' . $adminType . '_code';
		$moreCities = [
			'id'          => 0,
			'name'        => t('more_cities') . ' &raquo;',
			$adminCodeCol => 0,
		];
		$cities = $cities->push($moreCities);
		
		// Save cities
		$data['cities'] = $cities->toArray();
		
		// Get cities number of columns
		$numberOfCols = 4;
		if (data_get($values, 'enable_map') == '1') {
			$countryCode = strtolower(config('country.code'));
			$mapFilePath = config('larapen.core.maps.path') . $countryCode . '.svg';
			if (file_exists($mapFilePath)) {
				$numberOfCols = data_get($values, 'items_cols');
				$numberOfCols = !empty($numberOfCols) ? (int)$numberOfCols : 3;
			}
		}
		$data['items_cols'] = $numberOfCols;
		
		return $data;
	}
	
	/**
	 * Get premium listings
	 *
	 * @param array|null $values
	 * @return array
	 */
	protected function premiumListings(?array $values = []): array
	{
		$freeListingsInPremium = config('settings.listings_list.free_listings_in_premium');
		config()->set('settings.listings_list.free_listings_in_premium', '0');
		
		$listingsSection = $this->getListingsSection('premium', $values);
		
		config()->set('settings.listings_list.free_listings_in_premium', $freeListingsInPremium);
		
		return $listingsSection;
	}
	
	/**
	 * Get latest listings
	 *
	 * @param array|null $values
	 * @return array
	 */
	protected function latestListings(?array $values = []): array
	{
		return $this->getListingsSection('latest', $values);
	}
	
	/**
	 * Get listings' section
	 *
	 * @param string $op
	 * @param array|null $setting
	 * @return array
	 */
	private function getListingsSection(string $op = 'latest', ?array $setting = []): array
	{
		$data = [];
		
		if (!in_array($op, ['latest', 'premium'])) return $data;
		
		// Get the section's settings
		$cacheExpiration = (int)($setting['cache_expiration'] ?? 0);
		$maxItems = (int)($setting['max_items'] ?? 12);
		$orderBy = ($op == 'premium') ? 'random' : 'date';
		$orderBy = $setting['order_by'] ?? $orderBy;
		$embed = [
			'user',
			'category',
			'parent',
			'postType',
			'city',
			'savedByLoggedUser',
			'picture',
			'pictures',
			'payment',
			'package',
		];
		
		// Get the listings
		$input = [
			'op'              => $op,
			'cacheExpiration' => $cacheExpiration,
			'perPage'         => $maxItems,
			'embed'           => implode(',', $embed),
			'orderBy'         => $orderBy,
		];
		
		// Search
		$searchData = (new PostQueries($input))->fetch();
		
		$postsResult = data_get($searchData, 'posts', []);
		$posts = data_get($postsResult, 'data', []);
		$totalPosts = data_get($postsResult, 'meta.total', 0);
		
		// Get the section's data
		$section = null;
		if ($totalPosts > 0) {
			$title = ($orderBy == 'random') ? t('Home - Random Listings') : t('Home - Latest Listings');
			$title = ($op == 'premium') ? t('Home - Premium Listings') : $title;
			
			$url = urlGen()->searchWithoutQuery();
			if ($op == 'premium') {
				$url = urlQuery($url)->setParameters(['filterBy' => $op])->toString();
			}
			
			$section = [
				'title'      => $title,
				'link'       => $url,
				'posts'      => $posts,
				'totalPosts' => $totalPosts,
			];
		}
		
		$data[$op] = $section;
		
		return $data;
	}
	
	/**
	 * Get list of categories
	 *
	 * @param array|null $values
	 * @return array
	 */
	protected function categories(?array $values = []): array
	{
		$data = [];
		
		$cacheExpiration = (int)($values['cache_expiration'] ?? 0);
		$maxItems = (int)($values['max_items'] ?? null);
		$catDisplayType = $values['cat_display_type'] ?? 'c_bigIcon_list';
		$numberOfCols = 3;
		
		$cacheId = 'categories.parents.' . config('app.locale') . '.' . $catDisplayType . '.take.' . $maxItems;
		
		if (in_array($catDisplayType, ['cc_normal_list', 'cc_normal_list_s'])) {
			
			$categories = cache()->remember($cacheId, $cacheExpiration, function () {
				return Category::query()->orderBy('lft')->get();
			});
			$categories = collect($categories)->keyBy('id');
			$categories = $subCategories = $categories->groupBy('parent_id');
			
			if ($categories->has(null)) {
				$categories = !empty($maxItems)
					? $categories->get(null)->take($maxItems)
					: $categories->get(null);
				$subCategories = $subCategories->forget(null);
			} else {
				$categories = collect();
				$subCategories = collect();
			}
			
			$data['categories'] = $categories;
			$data['subCategories'] = $subCategories;
			
		} else {
			
			$categories = cache()->remember($cacheId, $cacheExpiration, function () use ($maxItems) {
				$categories = Category::query()->roots();
				if (!empty($maxItems)) {
					$categories = $categories->take($maxItems);
				}
				
				return $categories->orderBy('lft')->get();
			});
			$categories = collect($categories)->keyBy('id');
			
			$data['categories'] = $categories;
			
		}
		
		// Count Posts by category (if the option is enabled)
		$countPostsPerCat = [];
		if (config('settings.listings_list.count_categories_listings')) {
			$cacheId = config('country.code') . '.count.posts.per.cat.' . config('app.locale');
			$countPostsPerCat = cache()->remember($cacheId, $cacheExpiration, function () {
				return Category::countPostsPerCategory();
			});
		}
		
		$data['countPostsPerCat'] = $countPostsPerCat;
		
		return $data;
	}
	
	/**
	 * Get mini stats data
	 *
	 * @param array|null $values
	 * @return array
	 */
	protected function stats(?array $values = []): array
	{
		$cacheExpiration = (int)($values['cache_expiration'] ?? 0);
		
		// Count Posts
		$countPosts = ($values['custom_counts_listings'] ?? 0);
		if (empty($countPosts)) {
			$cacheId = config('country.code') . '.count.posts';
			$countPosts = cache()->remember($cacheId, $cacheExpiration, function () {
				return Post::query()->inCountry()->unarchived()->count();
			});
		}
		
		// Count Users
		$countUsers = ($values['custom_counts_users'] ?? 0);
		if (empty($countUsers)) {
			$cacheId = 'count.users';
			$countUsers = cache()->remember($cacheId, $cacheExpiration, function () {
				return User::query()->count();
			});
		}
		
		// Count Locations (Cities)
		$countLocations = ($values['custom_counts_locations'] ?? 0);
		if (empty($countLocations)) {
			$cacheId = config('country.code') . '.count.cities';
			$countLocations = cache()->remember($cacheId, $cacheExpiration, function () {
				return City::query()->inCountry()->count();
			});
		}
		
		return [
			'count' => [
				'posts'     => $countPosts,
				'users'     => $countUsers,
				'locations' => $countLocations,
			],
		];
	}
	
	/**
	 * Get the text area data
	 *
	 * @param array|null $values
	 * @return array
	 */
	protected function textArea(?array $values = []): array
	{
		return [];
	}
	
	/**
	 * @param array|null $values
	 * @return array
	 */
	protected function topAd(?array $values = []): array
	{
		$cacheId = 'advertising.top';
		$topAdvertising = cache()->remember($cacheId, $this->cacheExpiration, function () {
			return Advertising::query()
				->where('integration', 'unitSlot')
				->where('slug', 'top')
				->first();
		});
		
		return [
			'topAdvertising' => $topAdvertising,
		];
	}
	
	/**
	 * @param array|null $values
	 * @return array
	 */
	protected function bottomAd(?array $values = []): array
	{
		$cacheId = 'advertising.bottom';
		$bottomAdvertising = cache()->remember($cacheId, $this->cacheExpiration, function () {
			return Advertising::query()
				->where('integration', 'unitSlot')
				->where('slug', 'bottom')
				->first();
		});
		
		return [
			'bottomAdvertising' => $bottomAdvertising,
		];
	}
}
