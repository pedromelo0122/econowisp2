@php
	// Logo
	$logoFactoryUrl = config('larapen.media.logo-factory');
	$logoDarkUrl = config('settings.app.logo_dark_url', $logoFactoryUrl);
	$logoLightUrl = config('settings.app.logo_light_url', $logoFactoryUrl);
	$logoAlt = strtolower(config('settings.app.name'));
	$logoWidth = (int)config('settings.upload.img_resize_logo_width', 454);
	$logoHeight = (int)config('settings.upload.img_resize_logo_height', 80);
	
	// Theme Preference (light/dark/system)
	$showIconOnly ??= false;
	
	// Header & Navbar design parameters
	$defaultBgColorClass = 'bg-body-tertiary';
	$borderBottomClass = 'border-bottom';
	$animationStartingClass = 'navbar-sticky';
	$shadowClass = 'shadow';
	
	$bgColorClass = config('settings.header.background_class') ?? $defaultBgColorClass;
	$bgColorClass = !empty($bgColorClass) ? $bgColorClass : $defaultBgColorClass;
	
	$isHeaderDarkThemeEnabled = (config('settings.header.dark') == '1');
	$headerThemeAttr = $isHeaderDarkThemeEnabled ? ' data-bs-theme="dark"' : '';
	
	$isHeaderAnimationEnabled = (config('settings.header.animation') == '1');
	$animationClass = $isHeaderAnimationEnabled ? " {$animationStartingClass}" : '';
	
	$isHeaderShadowEnabled = (config('settings.header.shadow') == '1');
	$shadowClassEnabled = $isHeaderShadowEnabled ? " {$shadowClass}" : '';
	
	$isFixedTopHeader = (config('settings.header.fixed_top') == '1');
	$fixedTopClass = $isFixedTopHeader ? ' fixed-top' : '';
	
	$navbarClass = "{$fixedTopClass}{$shadowClassEnabled}{$animationClass} {$bgColorClass} {$borderBottomClass}";
	
	$isFullWidthHeader = (config('settings.header.full_width') == '1');
	$containerClass = $isFullWidthHeader ? 'container-fluid' : 'container';
@endphp
<header{!! $headerThemeAttr !!}>
	<nav class="navbar{{ $navbarClass }} navbar-expand-md" role="navigation" id="mainNavbar">
		<div class="{{ $containerClass }}">
			
			{{-- Logo --}}
			<a href="{{ url('/') }}" class="navbar-brand logo logo-title">
				<img src="{{ $logoDarkUrl }}"
				     alt="{{ $logoAlt }}"
				     class="main-logo dark-logo"
				     style="max-width: {{ $logoWidth }}px; max-height: {{ $logoHeight }}px; width:auto;"
				/>
				<img src="{{ $logoLightUrl }}"
				     alt="{{ $logoAlt }}"
				     class="main-logo light-logo"
				     style="max-width: {{ $logoWidth }}px; max-height: {{ $logoHeight }}px; width:auto;"
				/>
			</a>
			
			{{-- Toggle Nav (Mobile) --}}
			<button class="navbar-toggler float-end"
			        type="button"
			        data-bs-toggle="collapse"
			        data-bs-target="#navbarNav"
			        aria-controls="navbarNav"
			        aria-expanded="false"
			        aria-label="Toggle navigation"
			>
				<span class="navbar-toggler-icon"></span>
			</button>
			
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav me-md-auto">
					{{----}}
				</ul>
				
				<ul class="navbar-nav ms-auto">
					@if (isSettingsAppDarkModeEnabled())
						@include('front.layouts.partials.navs.themes', [
							'dropdownTag'   => 'li',
							'dropdownClass' => 'nav-item',
							'buttonClass'   => 'nav-link',
							'menuAlignment' => 'dropdown-menu-end',
							'showIconOnly'  => $showIconOnly,
						])
					@endif
					@include('front.layouts.partials.navs.languages')
				</ul>
			</div>
			
			
		</div>
	</nav>
</header>
@php
	$navbarHeightOffset = config('settings.header.height_offset');
	$navbarHeightOffset = (!empty($navbarHeightOffset) && is_numeric($navbarHeightOffset)) ? $navbarHeightOffset : 'null';
	
	$bgColorClass = config('settings.header.background_class') ?? $defaultBgColorClass;
	$bgColorClass = !empty($bgColorClass) ? $bgColorClass : $defaultBgColorClass;
	$bgColor = config('settings.header.background_color');
	$borderBottomWidth = config('settings.header.border_bottom_width');
	$borderBottomColor = config('settings.header.border_bottom_color');
	$linksColor = config('settings.header.link_color');
	$linksColorHover = config('settings.header.link_color_hover');
	
	$isFixedHeaderDarkThemeEnabled = (config('settings.header.fixed_dark') == '1');
	$isFixedHeaderShadowEnabled = (config('settings.header.fixed_shadow') == '1');
	$fixedBgColorClass = config('settings.header.fixed_background_class') ?? $defaultBgColorClass;
	$fixedBgColorClass = !empty($fixedBgColorClass) ? $fixedBgColorClass : $defaultBgColorClass;
	$fixedBgColor = config('settings.header.fixed_background_color');
	$fixedBorderBottomWidth = config('settings.style.fixed_header_border_bottom_width');
	$fixedBorderBottomColor = config('settings.header.fixed_border_bottom_color');
	$fixedLinksColor = config('settings.header.fixed_link_color');
	$fixedLinksColorHover = config('settings.header.fixed_link_color_hover');
@endphp
@pushonce('before_scripts_stack')
	<script>
		if (typeof window.headerOptions === 'undefined') {
			window.headerOptions = {};
		}
		window.headerOptions = {
			animationEnabled: {{ $isHeaderAnimationEnabled ? 'true' : 'false' }},
			navbarHeightOffset: {{ $navbarHeightOffset }},
			default: {
				darkThemeEnabled: {{ $isHeaderDarkThemeEnabled ? 'true' : 'false' }},
				bgColorClass: '{{ $bgColorClass }}',
				borderBottomClass: '{{ $borderBottomClass }}',
				shadowClass: '{{ $isHeaderShadowEnabled ? $shadowClass : '' }}',
				bgColor: '{{ $bgColor }}',
				borderBottomWidth: '{{ $borderBottomWidth }}',
				borderBottomColor: '{{ $borderBottomColor }}',
				linksColor: '{{ $linksColor }}',
				linksColorHover: '{{ $linksColorHover }}',
			},
			fixed: {
				enabled: {{ $isFixedTopHeader ? 'true' : 'false' }},
				darkThemeEnabled: {{ $isFixedHeaderDarkThemeEnabled ? 'true' : 'false' }},
				bgColorClass: '{{ $fixedBgColorClass }}',
				borderBottomClass: null,
				shadowClass: '{{ $isFixedHeaderShadowEnabled ? $shadowClass : '' }}',
				bgColor: '{{ $fixedBgColor }}',
				borderBottomWidth: '{{ $fixedBorderBottomWidth }}',
				borderBottomColor: '{{ $fixedBorderBottomColor }}',
				linksColor: '{{ $fixedLinksColor }}',
				linksColorHover: '{{ $fixedLinksColorHover }}',
			},
		};
	</script>
@endpushonce
