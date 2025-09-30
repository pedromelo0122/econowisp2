@props(['breadcrumbs'])

<nav aria-label="breadcrumb">
	@if (config('breadcrumbs.style') === 'bootstrap')
		<ol class="breadcrumb pb-0 mb-0">
			@foreach ($breadcrumbs as $index => $item)
				<li class="breadcrumb-item{{ $loop->last ? ' active' : '' }}"
				    @if ($loop->last) aria-current="page" @endif>
					@if ($item['url'] && !$loop->last)
						<a href="{{ $item['url'] }}" class="link-primary text-decoration-none">{{ $item['title'] }}</a>
					@else
						{{ $item['title'] }}
					@endif
				</li>
			@endforeach
		</ol>
	@else
		<ol class="breadcrumb">
			@foreach ($breadcrumbs as $index => $item)
				@if ($item['url'] && !$loop->last)
					<li class="breadcrumb-item">
						<a href="{{ $item['url'] }}" class="link-primary text-decoration-none">{{ $item['title'] }}</a>
					</li>
				@else
					<li class="breadcrumb-item active" aria-current="page">
						{{ $item['title'] }}
					</li>
				@endif
				@if (!$loop->last)
					<span class="breadcrumb-separator">{{ config('breadcrumbs.separator') }}</span>
				@endif
			@endforeach
		</ol>
	@endif
</nav>

@if (config('breadcrumbs.style') === 'custom' && config('breadcrumbs.css'))
	<link rel="stylesheet" href="{{ config('breadcrumbs.css') }}">
@endif
