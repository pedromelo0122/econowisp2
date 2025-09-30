@php
	$sectionOptions = $searchFormOptions ?? [];
	$sectionData ??= [];
	
	// Get Search Form Options
	$enableFormAreaCustomization = data_get($sectionOptions, 'enable_extended_form_area') ?? '0';
	$hideTitles = data_get($sectionOptions, 'hide_titles') ?? '0';
	
	$headerTitle = data_get($sectionOptions, 'title_' . config('app.locale'));
	$headerTitle = (!empty($headerTitle)) ? replaceGlobalPatterns($headerTitle) : null;
	
	$headerSubTitle = data_get($sectionOptions, 'sub_title_' . config('app.locale'));
	$headerSubTitle = (!empty($headerSubTitle)) ? replaceGlobalPatterns($headerSubTitle) : null;
	
	$parallax = data_get($sectionOptions, 'parallax') ?? '0';
	$hideForm = data_get($sectionOptions, 'hide_form') ?? '0';
	
	$isAutocompleteEnabled = (config('settings.listings_list.enable_cities_autocompletion') == '1');
	$autocompleteClass = $isAutocompleteEnabled ? ' autocomplete-enabled' : '';
	
	$statesSearchTip = t('states_search_tip', ['prefix' => t('area'), 'suffix' => t('state_name')]);
	$displayStatesSearchTip = config('settings.listings_list.display_states_search_tip');
	$searchTooltip = $displayStatesSearchTip
		? ' data-bs-placement="top" data-bs-toggle="tooltipHover" title="' . $statesSearchTip . '"'
		: '';
	
	$cssClasses = $sectionOptions['css_classes'] ?? '';
	$cssClasses = !empty($cssClasses) ? " {$cssClasses}" : '';
@endphp
@if (isset($enableFormAreaCustomization) && $enableFormAreaCustomization == '1')
	@php
		$parallaxClass = ($parallax == '1') ? ' parallax' : '';
	@endphp
	<div class="hero-wrap bg-secondary d-flex align-items-center{{ $cssClasses . $parallaxClass }}">
		<div class="container text-center">
			
			@if ($hideTitles != '1')
				<h1 class="text-uppercase fw-bold text-white text-shadow">
					{{ $headerTitle }}
				</h1>
				<h5 class="fs-4 lead text-white text-shadow mb-3">
					{!! $headerSubTitle !!}
				</h5>
			@endif
			
			@if ($hideForm != '1')
				<div class="row d-flex justify-content-center">
					<div class="col-9">
						<form id="searchForm"
						      name="search"
						      action="{{ urlGen()->searchWithoutQuery() }}"
						      method="GET"
						      class="home-search-form"
						      data-csrf-token="{{ csrf_token() }}"
						>
							<div class="w-100">
								@include('front.sections.home.search-form.form-fields')
							</div>
						</form>
					</div>
				</div>
			@endif
			
		</div>
	</div>
	
@else
	
	<div class="d-flex align-items-center only-search-bar{{ $cssClasses }}">
		<div class="container text-center">
			
			@if ($hideForm != '1')
				<div class="row d-flex justify-content-center px-2">
					<div class="col-12">
						<form id="searchForm"
						      name="search"
						      action="{{ urlGen()->searchWithoutQuery() }}"
						      method="GET"
						      class="home-search-form"
						      data-csrf-token="{{ csrf_token() }}"
						>
							<div class="w-100">
								@include('front.sections.home.search-form.form-fields')
							</div>
						</form>
					</div>
				</div>
			@endif
			
		</div>
	</div>
	
@endif
