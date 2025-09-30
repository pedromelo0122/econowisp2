@php
	$post ??= [];
	
	$userIsAdmin = (
		auth()->check()
		&& auth()->user()->hasAllPermissions(\App\Models\Permission::getStaffPermissions())
	);
	$userIsTheListingOwner = (
		auth()->check()
		&& isset(auth()->user()->id, $post)
		&& auth()->user()->id == data_get($post, 'user.id')
	);
	$userIsNotTheListingOwner = (
		auth()->check()
		&& isset(auth()->user()->id, $post)
		&& auth()->user()->id != data_get($post, 'user.id')
	);
	$guestCanPublishComment = (!auth()->check() && config('settings.reviews.guests_comments'));
@endphp
<div class="tab-pane reviews-widget"
	 id="item-{{ config('plugins.reviews.name') }}"
	 role="tabpanel"
	 aria-labelledby="item-{{ config('plugins.reviews.name') }}-tab"
>
	@if (!empty($post))
		<div class="container rounded bg-body-tertiary p-4 p-lg-3 p-md-2 mb-sm-3" id="reviews-anchor">
			@if (isset($errors) && $errors->any())
				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-danger alert-dismissible mb-0">
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ t('Close') }}"></button>
							<h5><strong>{{ trans('reviews::messages.There were errors while submitting this review') }}:</strong></h5>
							<ul class="list-unstyled">
								@foreach ($errors->all() as $error)
									<li class="ps-2"><i class="bi bi-check-lg"></i> {{ $error }}</li>
								@endforeach
							</ul>
						</div>
					</div>
				</div>
			@endif
			
			@if (session()->has('review_posted'))
				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-success alert-dismissible mb-0">
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ t('Close') }}"></button>
							<p class="mb-0">{{ trans('reviews::messages.review_posted') }}</p>
						</div>
					</div>
				</div>
			@endif
			
			@if (session()->has('review_removed'))
				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-success alert-dismissible mb-0">
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ t('Close') }}"></button>
							<p class="mb-0">{{ trans('reviews::messages.Your review has been removed!') }}</p>
						</div>
					</div>
				</div>
			@endif
			
			<div class="row mb-3 mt-3" id="post-review-box">
				@if (!auth()->check() && !config('settings.reviews.guests_comments'))
					<div class="col-md-12 pb-3">
						<div class="row d-flex justify-content-center">
							<div class="col-12 text-center my-3">
								<strong>{{ trans('reviews::messages.Note') }}:</strong>
								{{ trans('reviews::messages.You must be logged in to post a review.') }}
							</div>
							<div class="col-lg-7 col-md-8 col-sm-12">
								<form action="{{ urlGen()->signIn() }}" method="post" class="m-0 p-0">
									@csrf
									<div class="row gx-1 gy-1">
										
										{{-- email --}}
										@php
											$labelRight = '';
											if (isPhoneAsAuthFieldEnabled()) {
												$labelRight .= '<a href="" class="auth-field ' . linkClass()  . '" data-auth-field="phone">';
												$labelRight .= trans('auth.login_with_phone');
												$labelRight .= '</a>';
											}
											$emailValue = session()->has('email') ? session('email') : null;
										@endphp
										@include('helpers.forms.fields.text', [
											'label'             => trans('auth.email'),
											'labelRightContent' => $labelRight,
											'id'                => 'email',
											'name'              => 'email',
											'required'          => (getAuthField() == 'email'),
											'placeholder'       => trans('auth.email_or_username'),
											'value'             => $emailValue,
											'wrapper'           => ['class' => 'auth-field-item'],
										])
										
										{{-- phone --}}
										@if (isPhoneAsAuthFieldEnabled())
											@php
												$labelRight = '<a href="" class="auth-field ' . linkClass()  . '" data-auth-field="email">';
												$labelRight .= trans('auth.login_with_email');
												$labelRight .= '</a>';
												
												$phoneValue = session()->has('phone') ? session('phone') : null;
												$phoneCountryValue = config('country.code');
											@endphp
											@include('helpers.forms.fields.intl-tel-input', [
												'label'             => trans('auth.phone_number'),
												'labelRightContent' => $labelRight,
												'id'                => 'phone',
												'name'              => 'phone',
												'required'          => (getAuthField() == 'phone'),
												'placeholder'       => null,
												'value'             => $phoneValue,
												'countryCode'       => $phoneCountryValue,
												'wrapper'           => ['class' => 'auth-field-item'],
											])
										@endif
										
										{{-- auth_field --}}
										<input name="auth_field" type="hidden" value="{{ old('auth_field', getAuthField()) }}">
										
										{{-- password --}}
										@include('helpers.forms.fields.password', [
											'label'          => trans('auth.password'),
											'name'           => 'password',
											'placeholder'    => trans('auth.password'),
											'required'       => true,
											'value'          => null,
											'togglePassword' => 'link',
											'hint'           => false,
										])
										
										{{-- captcha --}}
										@include('helpers.forms.fields.captcha', ['label' => trans('auth.captcha_human_verification')])
										
										<div class="mb-3 col-12 d-grid">
											<button type="submit" class="btn btn-primary">
												{{ trans('auth.log_in') }}
											</button>
										</div>
										
									</div>
								</form>
							</div>
						</div>
					</div>
				@else
					@if ($userIsNotTheListingOwner || $guestCanPublishComment)
						<div class="col-md-12">
							<form id="reviewsForm" action="{{ url('posts/' . data_get($post, 'id') . '/reviews/create') }}" method="POST">
								@csrf
								<input type="hidden" name="rating" id="rating">
								
								@include('helpers.forms.fields.textarea', [
									'label'         => trans('reviews::messages.Review'),
									'name'          => 'comment',
									'placeholder'   => trans('reviews::messages.Enter your review here...'),
									'required'      => true,
									'value'         => null,
									'height'        => 100,
									'attributes'    => ['rows' => 5, 'style' => 'min-height: 100px;'],
								])
								
								<div class="row">
									<div class="col-md-12 text-right">
										<div class="stars starrr" data-rating="{{ old('rating', 0) }}"></div>
										<button class="btn btn-primary" type="submit">
											{{ trans('reviews::messages.Leave a Review') }}
										</button>
									</div>
								</div>
							</form>
						</div>
					@endif
				
				@endif
			</div>
			
			@if ($userIsNotTheListingOwner || $guestCanPublishComment)
				<hr class="border-0 bg-secondary">
			@endif
			
			@php
				$reviewsApiResult = $reviewsApiResult ?? [];
				$messageReviews = data_get($reviewsApiResult, 'message') ?? null;
				$reviewsApiResult = (array)data_get($reviewsApiResult, 'result');
				$reviews = (array)data_get($reviewsApiResult, 'data');
				$totalReviews = (int)data_get($reviewsApiResult, 'meta.total', 0);
			@endphp
			@if (!empty($reviews) && $totalReviews > 0)
				@foreach($reviews as $review)
					@php
						$userIsTheCommentOwner = (
							auth()->check()
							&& isset(auth()->user()->id)
							&& auth()->user()->id == data_get($review, 'user.id')
						);
						$createdAt = data_get($review, 'created_at_formatted', data_get($review, 'created_at'));
						$createdAt = !empty($createdAt) ? $createdAt : date('D, d M Y H:i:s');
					@endphp
					<div class="container border-top pt-3">
						<div class="row">
							<div class="col-md-12 hstack gap-2">
								<div class="hstack gap-2">
									<div>
										<span class="fw-bold">
											{{ data_get($review, 'user.name') ?? trans('reviews::messages.Anonymous') }}
										</span>
										@if ($userIsTheCommentOwner || $userIsAdmin)
											@php
												$deleteReviewUrl = url('posts/' . data_get($post, 'id') . '/reviews/' . data_get($review, 'id') . '/delete');
											@endphp
											[<a href="{{ $deleteReviewUrl }}" class="confirm-simple-action {{ linkClass() }}">
												{{ trans('reviews::messages.Delete') }}
											</a>]
										@endif
									</div>
									<div>
										@for ($i=1; $i <= 5 ; $i++)
											<span class="{{ ($i <= data_get($review, 'rating')) ? 'fas' : 'far'}} fa-star"></span>
										@endfor
									</div>
								</div>
								<div class="ms-auto small">{!! $createdAt !!}</div>
							</div>
							<div class="col-md-12">
								<p>{!! data_get($review, 'comment') !!}</p>
							</div>
						</div>
					</div>
				@endforeach
				
				<div class="mb-3">
					@include('vendor.pagination.api.bootstrap-5', ['apiResult' => $reviewsApiResult])
				</div>
			@else
				@if ($userIsTheListingOwner)
					<p>{{ trans('reviews::messages.Your listing has no reviews yet.') }}</p>
				@else
					@if (auth()->check() || config('settings.reviews.guests_comments'))
						<p>{{ trans('reviews::messages.This listing has no reviews yet. Be the first to leave a review.') }}</p>
					@endif
				@endif
			@endif
		</div>
	@endif
</div>

@section('after_styles')
	@parent
	<link href="{{ url('plugins/reviews/assets/js/starrr.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('after_scripts')
	@parent
	<script src="{{ url('plugins/reviews/assets/js/autosize.js') }}"></script>
	<script src="{{ url('plugins/reviews/assets/js/starrr.js') }}"></script>
	<script>
		$(document).ready(function () {
			{{-- Initialize the autosize plugin on the review text area --}}
			autosize($('#comment'));
			
			{{-- Bind the change event for the star rating - store the rating value in a hidden field --}}
			$('.starrr').starrr({
				change: function (e, value) {
					$('#rating').val(value);
				}
			});
		});
	</script>
@endsection
