@include('front.common.css.skin')

<style>
/* === Body === */

@php
	$isFixedTopHeader = (config('settings.header.fixed_top') == '1');
	
	// Logo Max Sizes
	$logoMaxWidth = config('larapen.media.resize.namedOptions.logo-max.width', 430);
	$logoMaxHeight = config('larapen.media.resize.namedOptions.logo-max.height', 80);
	if (!empty(config('settings.header.height'))) {
		$logoMaxHeight = forceToInt(config('settings.header.height'), $logoMaxHeight);
	}
	
	// Logo Sizes
	$logoWidth = forceToInt(config('settings.header.logo_width'), 216);
	$logoHeight = forceToInt(config('settings.header.logo_height'), 40);
	if (config('settings.header.logo_aspect_ratio')) {
		if ($logoHeight <= $logoWidth) {
			$logoWidth = 'auto';
			$logoHeight = $logoHeight . 'px';
		} else {
			$logoWidth = $logoWidth . 'px';
			$logoHeight = 'auto';
		}
	} else {
		$logoWidth = $logoWidth . 'px';
		$logoHeight = $logoHeight . 'px';
	}
@endphp
.main-logo {
	width: {{ $logoWidth }};
	height: {{ $logoHeight }};
	max-width: {{ $logoMaxWidth }}px !important;
	max-height: {{ $logoMaxHeight }}px !important;
}
@if (!empty(config('settings.style.page_width')))
	@php
		$pageWidth = forceToInt(config('settings.style.page_width')) . 'px';
	@endphp
	@media (min-width: 1200px) {
		.container {
			max-width: {{ $pageWidth }};
		}
	}
@endif
@if (
	!empty(config('settings.style.body_background_color'))
	|| !empty(config('settings.style.body_background_image_path'))
)
	body.bg-body {
		@if (!empty(config('settings.style.body_background_color')))
			background-color: {{ config('settings.style.body_background_color') }} !important;
		@endif
		@if (!empty(config('settings.style.body_background_image_url')))
			background-image: url({{ config('settings.style.body_background_image_url') }});
			
			@if (!empty(config('settings.style.body_background_image_position')))
				background-position: {{ config('settings.style.body_background_image_position') }};
			@endif
			@if (!empty(config('settings.style.body_background_image_size')))
				background-size: {{ config('settings.style.body_background_image_size') }};
			@endif
			@if (!empty(config('settings.style.body_background_image_repeat')))
				background-repeat: {{ config('settings.style.body_background_image_repeat') }};
			@endif
			@if (!empty(config('settings.style.body_background_image_attachment')))
				background-attachment: {{ config('settings.style.body_background_image_attachment') }};
			@endif
			@if (config('settings.style.body_background_image_animation') == '1')
				animation: zoom-bg-image 20s infinite alternate;
			@endif
		@endif
	}
@endif
@if (!empty(config('settings.style.body_text_color')))
	body.text-body-emphasis {
		color: {{ config('settings.style.body_text_color') }} !important;
	}
@endif

@if (!empty(config('settings.style.body_background_color')) || !empty(config('settings.style.body_background_image_path')))
	main {
		background-color: rgba(0, 0, 0, 0);
	}
@endif
@if (!empty(config('settings.style.title_color')))
	.skin h1,
	.skin h2,
	.skin h3,
	.skin h4,
	.skin h5,
	.skin h6 {
		color: {{ config('settings.style.title_color') }};
	}
@endif
@if (!empty(config('settings.style.link_color')))
	.skin a,
	.skin .link-color {
		color: {{ config('settings.style.link_color') }};
	}
@endif
@if (!empty(config('settings.style.link_color_hover')))
	.skin a:hover,
	.skin a:focus {
		color: {{ config('settings.style.link_color_hover') }};
	}
@endif
@if (!empty(config('settings.style.progress_background_color')))
	.skin .pace .pace-progress {
		background: {{ config('settings.style.progress_background_color') }} none repeat scroll 0 0;
	}
@endif

/* === Header === */
@if (!empty(config('settings.header.height')))
	@php
		// Default values
		$defaultHeight = 80;
		$defaultPadding = 20;
		$defaultMargin = 0;
		
		// Get known value from Settings
		$headerHeight = forceToInt(config('settings.header.height'));
		
		$headerBottomBorderSize = 0;
		if (!empty(config('settings.header.border_bottom_width'))) {
			$headerBottomBorderSize = forceToInt(config('settings.header.border_bottom_width'));
		}
		$wrapperPaddingTop = $headerHeight + $headerBottomBorderSize;
		
		// Calculate unknown values
		$calculatedPadding = floor(($headerHeight * $defaultPadding) / $defaultHeight);
		$calculatedMargin = floor(($headerHeight * $defaultMargin) / $defaultHeight);
		$padding = abs(($calculatedPadding - ($defaultPadding / 2)) * 2);
		$margin = abs(($calculatedMargin - ($defaultMargin / 2)) * 2);
		
		/* $wrapperPaddingTop + 4 for default margin/padding values */
		$wrapperPaddingTop = $wrapperPaddingTop + ($padding - 4);
	@endphp
	header .navbar {
		min-height: {{ $headerHeight }}px;
		padding-top: {{ $padding }}px;
		padding-bottom: {{ $padding }}px;
	}
	@if ($isFixedTopHeader)
		main {
			{{-- https://getbootstrap.com/docs/5.3/components/navbar/#placement --}}
			{{-- padding-top: {{ $wrapperPaddingTop }}px; --}}
		}
   @endif
@endif

/* === Footer === */
@if (!empty(config('settings.footer.background_color')))
	footer > div.bg-body-tertiary {
		background: {{ config('settings.footer.background_color') }} !important;
	}
@endif
@if (!empty(config('settings.footer.border_top_width')))
	@php
		$footerBorderTopSize = forceToInt(config('settings.footer.border_top_width')) . 'px';
	@endphp
	footer > div.bg-body-tertiary {
		border-top-width: {{ $footerBorderTopSize }} !important;
		border-top-style: solid !important;
	}
@endif
@if (!empty(config('settings.footer.border_top_color')))
	footer > div.bg-body-tertiary {
		border-top-color: {{ config('settings.footer.border_top_color') }} !important;
	}
@endif
@if (!empty(config('settings.footer.text_color')))
	footer > div {
		color: {{ config('settings.footer.text_color') }};
	}
@endif
@if (!empty(config('settings.footer.title_color')))
	footer h1, footer h2, footer h3, footer h4, footer h5, footer h6 {
		color: {{ config('settings.footer.title_color') }};
	}
@endif
@if (!empty(config('settings.footer.link_color')))
	footer a.link-body-emphasis,
	footer a.link-primary {
		color: {{ config('settings.footer.link_color') }} !important;
	}
@endif
@if (!empty(config('settings.footer.link_color_hover')))
	footer a.link-body-emphasis:hover,
	footer a.link-body-emphasis:focus,
	footer a.link-primary:hover,
	footer a.link-primary:focus {
		color: {{ config('settings.footer.link_color_hover') }} !important;
	}
@endif
@if (!empty(config('settings.footer.inside_line_border_color')))
	.payment-method-logo {
		border-top-color: {{ config('settings.footer.inside_line_border_color') }};
	}
@endif
</style>
