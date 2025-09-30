@php
	$sectionOptions = $bottomAdOptions ?? [];
	
	$cssClasses = $sectionOptions['css_classes'] ?? '';
	$cssClasses = !empty($cssClasses) ? " {$cssClasses}" : '';
@endphp
@include('front.layouts.partials.advertising.top')
