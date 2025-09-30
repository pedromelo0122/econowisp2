@php
    $post ??= [];
@endphp
@if (!empty($post) && !empty(data_get($post, 'user')))
    @php
        $userRating = data_get($post, 'p_user_rating', 0);
        $countUserRatings = data_get($post, 'p_count_user_ratings', 0);
        $ratingUnitLabel = trans_choice('reviews::messages.count_ratings', getPlural($countUserRatings), [], config('app.locale'));
		
		$marginDirection = (config('lang.direction') == 'rtl') ? 'right' : 'left';
	    $ml = "margin-$marginDirection: -5px !important;";
    @endphp
    <div class="border border-success rounded x-2 py-0 d-flex justify-content-center">
        <div class="">
            <p class="p-0 m-0">
                <span class="text-warning">
                    @for ($i=1; $i <= 5 ; $i++)
                        <i class="{{ ($i <= $userRating) ? 'fas' : 'far' }} fa-star" style="{!! $ml !!}"></i>
                    @endfor
                </span>
                <span class="text-success small">
                    {{ $countUserRatings  }} {{ mb_strtolower($ratingUnitLabel) }}
                </span>
            </p>
        </div>
    </div>
@endif
