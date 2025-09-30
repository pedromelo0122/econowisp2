@php
    $post ??= [];
	
	$marginDirection = (config('lang.direction') == 'rtl') ? 'right' : 'left';
	$ml = "margin-$marginDirection: -5px !important;";
@endphp
@if (!empty($post))
    <div class="w-100 info-row rating-list mt-2">
        @php
            $ratingCache = data_get($post, 'rating_cache', 0);
            $ratingCount = data_get($post, 'rating_count', 0);
        @endphp
        <span class="fs-6 text-warning">
            @for ($i=1; $i <= 5 ; $i++)
                <i class="{{ ($i <= $ratingCache) ? 'fas' : 'far' }} fa-star mx-0" style="{!! $ml !!}"></i>
            @endfor
        </span>
        <span class="text-secondary ms-1">
            {{ $ratingCount }} {{ trans_choice('reviews::messages.count_reviews', getPlural($ratingCount), [], config('app.locale')) }}
        </span>
    </div>
    
    @section('reviews_styles')
    @endsection
@endif
