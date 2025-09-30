@php
	$sectionOptions = $categoriesOptions ?? [];
	$sectionData ??= [];
	$categories = (array)data_get($sectionData, 'categories');
	$subCategories = (array)data_get($sectionData, 'subCategories');
	$countPostsPerCat = (array)data_get($sectionData, 'countPostsPerCat');
	$countPostsPerCat = collect($countPostsPerCat)->keyBy('id')->toArray();
	
	$catDisplayType = data_get($sectionOptions, 'cat_display_type');
	$maxSubCats = (int)data_get($sectionOptions, 'max_sub_cats');
	
	$cssClasses = $sectionOptions['css_classes'] ?? '';
	$cssClasses = !empty($cssClasses) ? " {$cssClasses}" : '';
@endphp

<div class="container{{ $cssClasses }}">
	<div class="card">
		
		<div class="card-header border-bottom-0">
			<h4 class="mb-0 float-start fw-lighter">
				{{ t('Browse by') }} <span class="fw-bold">{{ t('category') }}</span>
			</h4>
			<h5 class="mb-0 float-end mt-1 fs-6 fw-lighter text-uppercase">
				<a href="{{ urlGen()->sitemap() }}" class="{{ linkClass() }}">
					{{ t('View more') }} <i class="fa-solid fa-bars"></i>
				</a>
			</h5>
		</div>
		<div class="card-body rounded py-0">
			@if ($catDisplayType == 'c_picture_list')
				
				@include('front.sections.home.categories.c-picture-list')
			
			@elseif ($catDisplayType == 'c_bigIcon_list')
				
				@include('front.sections.home.categories.c-big-icon-list')
			
			@elseif (in_array($catDisplayType, ['cc_normal_list', 'cc_normal_list_s']))
				
				@include('front.sections.home.categories.cc-normal-list')
			
			@elseif (in_array($catDisplayType, ['c_normal_list', 'c_border_list']))
				
				@include('front.sections.home.categories.c-normal-list')
			
			@else
				
				{{-- Called only when issue occurred --}}
				@include('front.sections.home.categories.c-big-icon-list')
			
			@endif
		</div>
	
	</div>
</div>

@section('before_scripts')
	@parent
	@if ($maxSubCats >= 0)
		<script>
			var maxSubCats = {{ $maxSubCats }};
		</script>
	@endif
@endsection
@section('after_scripts')
	@parent
	<script>
		onDocumentReady((event) => {
			{{-- Category Title Animation --}}
			{{-- https://animate.style --}}
			const elements = document.querySelectorAll('.big-icon-category-list a h6, .picture-category-list a h6');
			if (elements.length) {
				const animation = 'animate__pulse';
				
				elements.forEach((element) => {
					element.addEventListener('mouseover', (event) => {
						event.target.classList.add('animate__animated', animation);
					});
					element.addEventListener("mouseout", (event) => {
						event.target.classList.remove('animate__animated', animation);
					});
				})
			}
		});
	</script>
@endsection
