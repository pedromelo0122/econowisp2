<?php

namespace Database\Seeders;

use App\Helpers\Common\NestedSetSeeder;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Package;
use App\Models\PaymentMethod;
use App\Models\Role;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->runWithMenuId();
	}
	
	/**
	 * @param int|null $opMenuId
	 * @return void
	 */
	public function runWithMenuId(?int $opMenuId = null): void
	{
		// $appUrl = env('APP_URL');
		$appUrl = config('app.url');
		$isDemoDomain = (isDemoDomain($appUrl) || isDevEnv($appUrl));
		
		// Get menu items parameters
		$conditionsForGuest = ['guest' => 'true'];
		$conditionsForUser = ['auth' => 'true'];
		$conditionsForAuthenticUser = array_merge($conditionsForUser, ['authentic' => 'true']);
		$conditionsForImpersonating = array_merge($conditionsForUser, ['impersonating' => 'true']);
		$rolesForAdminUser = [Role::getSuperAdminRole()];
		if ($isDemoDomain) {
			$demoAdminRole = 'admin';
			$rolesForAdminUser = collect($rolesForAdminUser)->add($demoAdminRole)->toArray();
		}
		
		// Get entries by menu location
		$entriesByMenu = [
			// HEADER
			'header' => [
				[
					'type'             => 'link',
					'icon'             => 'bi-grid-fill',
					'label'            => [
						'en' => "Browse Listings",
					],
					'url_type'         => 'route',
					'route_name'       => 'browse.listings',
					'route_parameters' => null,
					'url'              => null,
					'target'           => null,
					'nofollow'         => 0,
					'btn_class'        => null,
					'btn_outline'      => 0,
					'css_class'        => null,
					'conditions'       => null,
					'children'         => [],
				],
				[
					'type'             => 'link',
					'icon'             => 'fa-solid fa-tags',
					'label'            => [
						'en' => "Pricing",
					],
					'url_type'         => 'route',
					'route_name'       => 'pricing',
					'route_parameters' => null,
					'url'              => null,
					'target'           => null,
					'nofollow'         => 0,
					'btn_class'        => null,
					'btn_outline'      => 0,
					'css_class'        => null,
					'conditions'       => null,
					'children'         => [],
				],
				[
					'type'             => 'button',
					'icon'             => 'fa-solid fa-pen-to-square',
					'label'            => [
						'en' => "Create Listing",
					],
					'url_type'         => 'route',
					'route_name'       => 'listing.create.ms.showForm',
					'route_parameters' => null,
					'url'              => null,
					'target'           => null,
					'nofollow'         => 0,
					'btn_class'        => 'btn-highlight',
					'btn_outline'      => 0,
					'css_class'        => null,
					'conditions'       => null,
					'children'         => [],
				],
				// Auth Links (for Guests)
				[
					'type'             => 'link',
					'icon'             => 'fa-solid fa-user',
					'label'            => [
						'en' => "Log In",
					],
					'url_type'         => 'internal',
					'route_name'       => null,
					'route_parameters' => null,
					'url'              => '#',
					'target'           => null,
					'nofollow'         => 0,
					'btn_class'        => null,
					'btn_outline'      => 0,
					'css_class'        => null,
					'conditions'       => $conditionsForGuest,
					'description'      => 'Only for guests',
					'children'         => [
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Log In",
							],
							'url_type'         => 'route',
							'route_name'       => 'auth.login.showForm',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForGuest,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Sign Up",
							],
							'url_type'         => 'route',
							'route_name'       => 'auth.register.showForm',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForGuest,
							'children'         => [],
						],
					],
				],
				// Account Links (for Users)
				[
					'type'             => 'link',
					'icon'             => 'bi-person-circle',
					'label'            => [
						'en' => "{user.name}",
					],
					'url_type'         => 'internal',
					'route_name'       => null,
					'route_parameters' => null,
					'url'              => '#',
					'target'           => null,
					'nofollow'         => 0,
					'btn_class'        => null,
					'btn_outline'      => 0,
					'css_class'        => null,
					'conditions'       => $conditionsForUser,
					'description'      => 'Only for logged-in users',
					'children'         => [
						[
							'type'             => 'link',
							'icon'             => 'fa-solid fa-list',
							'label'            => [
								'en' => "My listings",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.listings.online',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => 'bi-hourglass-split',
							'label'            => [
								'en' => "Pending approval",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.listings.pendingApproval',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => 'bi-bookmarks',
							'label'            => [
								'en' => "Favourite listings",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.savedListings',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => 'bi-chat-text',
							'label'            => [
								'en' => "Messenger",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.messages',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'children'         => [],
						],
						[
							'type'             => 'divider',
							'icon'             => null,
							'label'            => null,
							'url_type'         => null,
							'route_name'       => null,
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'children'         => null,
						],
						[
							'type'             => 'link',
							'icon'             => 'bi-person-lines-fill',
							'label'            => [
								'en' => "My account",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.overview',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => 'bi-person-circle',
							'label'            => [
								'en' => "Profile",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.profile',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => 'bi-shield-lock',
							'label'            => [
								'en' => "Security",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.security',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => 'bi-sliders',
							'label'            => [
								'en' => "Preferences",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.preferences',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => 'bi-box-arrow-right',
							'label'            => [
								'en' => "Log Out",
							],
							'url_type'         => 'route',
							'route_name'       => 'auth.logout',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForAuthenticUser,
							'description'      => 'Only for authentic users (Not impersonated)',
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => 'bi-box-arrow-right',
							'label'            => [
								'en' => "Leave",
							],
							'url_type'         => 'route',
							'route_name'       => 'impersonate.leave',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForImpersonating,
							'description'      => 'Only for impersonated users',
							'children'         => [],
						],
						[
							'type'             => 'divider',
							'icon'             => null,
							'label'            => null,
							'url_type'         => null,
							'route_name'       => null,
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForAuthenticUser,
							'roles'            => $rolesForAdminUser,
							'description'      => 'Only for admin users (with the &quot;super-admin&quot; role)',
							'children'         => null,
						],
						[
							'type'             => 'link',
							'icon'             => 'fa-solid fa-gears',
							'label'            => [
								'en' => "Admin Panel",
							],
							'url_type'         => 'route',
							'route_name'       => 'admin.panel',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForAuthenticUser,
							'roles'            => $rolesForAdminUser,
							'description'      => 'Only for admin users (with the &quot;super-admin&quot; role)',
							'children'         => [],
						],
					],
				],
			],
			
			// FOOTER
			'footer' => [
				[
					'type'             => 'title',
					'icon'             => null,
					'label'            => [
						'en' => "About Us",
					],
					'url_type'         => null,
					'route_name'       => null,
					'route_parameters' => null,
					'url'              => null,
					'target'           => null,
					'nofollow'         => 0,
					'btn_class'        => null,
					'btn_outline'      => 0,
					'css_class'        => null,
					'conditions'       => null,
					'children'         => [
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Terms",
							],
							'url_type'         => 'route',
							'route_name'       => 'page.terms',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => null,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "FAQ",
							],
							'url_type'         => 'route',
							'route_name'       => 'page.faq',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => null,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Anti-Scam",
							],
							'url_type'         => 'route',
							'route_name'       => 'page.anti-scam',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => null,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Privacy",
							],
							'url_type'         => 'route',
							'route_name'       => 'page.privacy',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => null,
							'children'         => [],
						],
					],
				],
				[
					'type'             => 'title',
					'icon'             => null,
					'label'            => [
						'en' => "Contact & Sitemap",
					],
					'url_type'         => null,
					'route_name'       => null,
					'route_parameters' => null,
					'url'              => null,
					'target'           => null,
					'nofollow'         => 0,
					'btn_class'        => null,
					'btn_outline'      => 0,
					'css_class'        => null,
					'conditions'       => null,
					'children'         => [
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Contact Us",
							],
							'url_type'         => 'route',
							'route_name'       => 'contact.showForm',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => null,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Sitemap",
							],
							'url_type'         => 'route',
							'route_name'       => 'sitemap',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => null,
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Countries",
							],
							'url_type'         => 'route',
							'route_name'       => 'country.list',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => null,
							'children'         => [],
						],
					],
				],
				[
					'type'             => 'title',
					'icon'             => null,
					'label'            => [
						'en' => "My Account",
					],
					'url_type'         => null,
					'route_name'       => null,
					'route_parameters' => null,
					'url'              => null,
					'target'           => null,
					'nofollow'         => 0,
					'btn_class'        => null,
					'btn_outline'      => 0,
					'css_class'        => null,
					'conditions'       => null,
					'children'         => [
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Log In",
							],
							'url_type'         => 'route',
							'route_name'       => 'auth.login.showForm',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForGuest,
							'description'      => 'Only for guests',
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Register",
							],
							'url_type'         => 'route',
							'route_name'       => 'auth.register.showForm',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForGuest,
							'description'      => 'Only for guests',
							'children'         => [],
						],
						
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "My Account",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.overview',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'description'      => 'Only for logged-in users',
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "My Listings",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.listings.online',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'description'      => 'Only for logged-in users',
							'children'         => [],
						],
						[
							'type'             => 'link',
							'icon'             => null,
							'label'            => [
								'en' => "Favourite Listings",
							],
							'url_type'         => 'route',
							'route_name'       => 'account.savedListings',
							'route_parameters' => null,
							'url'              => null,
							'target'           => null,
							'nofollow'         => 0,
							'btn_class'        => null,
							'btn_outline'      => 0,
							'css_class'        => null,
							'conditions'       => $conditionsForUser,
							'description'      => 'Only for logged-in users',
							'children'         => [],
						],
					],
				],
			],
		];
		
		// Get the argument passed from admin controller
		$opMenuId = getAsInt($opMenuId);
		
		// Get all menus
		$menus = Menu::query()->get();
		if ($menus->count() <= 0) {
			return;
		}
		
		// Get menus locations and they ID
		// i.e. Retrieve Menu ID by the $entriesByMenu key/location
		$menuLocations = $menus->pluck('id', 'location')->toArray();
		
		// Filter the $entriesByMenu with the requested Menu ID
		if (!empty($opMenuId)) {
			$menuIdsByLocation = collect($menuLocations)->flip()->toArray();
			$menuLocationFromDb = $menuIdsByLocation[$opMenuId] ?? null;
			
			$entriesByMenu = collect($entriesByMenu)
				->filter(function ($item, $key) use ($menuLocationFromDb) {
					return empty($menuLocationFromDb) || $key === $menuLocationFromDb;
				})->toArray();
		}
		
		// ---
		
		$tableName = (new MenuItem())->getTable();
		
		// Count activated packages & payment methods (to activate/disable the 'pricing' route)
		$countPackages = Package::query()->active()->count();
		$countPaymentMethods = PaymentMethod::query()->active()->count();
		$isPricingPageReady = ($countPackages > 0 && $countPaymentMethods > 0);
		
		// Get the app's timezone
		$timezone = config('app.timezone', 'UTC');
		
		// Process entries for each menu location
		foreach ($entriesByMenu as $menuLocation => $entries) {
			$menuIdFromDb = $menuLocations[$menuLocation] ?? null;
			
			// Process all entries (including nested children) recursively
			$processedEntries = $this->processMenuItemEntries(
				entries: $entries,
				menuId: $menuIdFromDb,
				depth: 0,
				isPricingPageReady: $isPricingPageReady,
				timezone: $timezone
			);
			
			$startPosition = NestedSetSeeder::getNextRgtValue($tableName);
			NestedSetSeeder::insertEntries($tableName, $processedEntries, $startPosition);
		}
	}
	
	/**
	 * Recursively process menu-item entries and their children
	 *
	 * @param array $entries The menu-item entries to process
	 * @param int|null $menuId The menu ID to assign
	 * @param int $depth The current depth level (0 for root)
	 * @param bool $isPricingPageReady Check if the pricing page is ready or not
	 * @param string $timezone Timezone for timestamps
	 * @return array Processed entries
	 */
	private function processMenuItemEntries(array $entries, ?int $menuId, int $depth, bool $isPricingPageReady, string $timezone): array
	{
		$processedEntries = [];
		
		foreach ($entries as $key => $entry) {
			// Set menu_id for current entry
			$entry['menu_id'] = $menuId ?? $entry['menu_id'] ?? null;
			
			// Process children recursively if they exist
			$children = $entry['children'] ?? [];
			if (!empty($children) && is_array($children)) {
				$entry['children'] = $this->processMenuItemEntries(
					entries: $children,
					menuId: $entry['menu_id'],
					depth: $depth + 1,
					isPricingPageReady: $isPricingPageReady,
					timezone: $timezone
				);
			}
			
			// Set active status based on route and package count
			$active = 1;
			$routeName = $entry['route_name'] ?? null;
			if ($routeName === 'pricing') {
				$active = $isPricingPageReady ? 1 : 0;
			}
			
			// Set common entry properties
			$entry['parent_id'] = null;
			$entry['lft'] = 0;
			$entry['rgt'] = 0;
			$entry['depth'] = $depth;
			$entry['active'] = $active;
			$entry['created_at'] = now($timezone)->format('Y-m-d H:i:s');
			$entry['updated_at'] = null;
			
			$processedEntries[$key] = $entry;
		}
		
		return $processedEntries;
	}
}
