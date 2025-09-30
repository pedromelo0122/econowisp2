{{--
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: Mayeul Akpovi (BeDigit - https://bedigit.com)
 *
 * LICENSE
 * -------
 * This software is provided under a license agreement and may only be used or copied
 * in accordance with its terms, including the inclusion of the above copyright notice.
 * As this software is sold exclusively on CodeCanyon,
 * please review the full license details here: https://codecanyon.net/licenses/standard
--}}
@extends('front.layouts.master')

@php
	$authUser ??= auth()->user();
@endphp
@section('content')
	@include('front.common.spacer')
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('front.account.partials.sidebar')
				</div>
				
				<div class="col-md-9">
					
					@include('flash::message')
					
					@if (isset($errors) && $errors->any())
						<div class="alert alert-danger alert-dismissible">
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ t('Close') }}"></button>
							<h5 class="fw-bold text-danger-emphasis mb-3">
								{{ t('validation_errors_title') }}
							</h5>
							<ul class="mb-0 list-unstyled">
								@foreach ($errors->all() as $error)
									<li class="lh-lg"><i class="bi bi-check-lg me-1"></i>{!! $error !!}</li>
								@endforeach
							</ul>
						</div>
					@endif
					
					{{-- Photo upload fileinput messages handlers --}}
					<div id="avatarUploadError" class="center-block" style="width:100%; display:none"></div>
					<div id="avatarUploadSuccess" class="alert alert-success fade show" style="display:none;"></div>
					
					@include('front.account.partials.header', [
						'headerTitle' => '<i class="bi bi-person-circle"></i> ' . trans('auth.profile')
					])
					@php
    $profileUser = isset($user) ? $user : ($authUser ?? null);
@endphp

@if(!empty($profileUser) && !empty($profileUser->trusted_seller))
    <div class="mb-3">
        <span class="badge badge-success" title="Vendedor de confianza" style="display:inline-flex;align-items:center;gap:.35rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 2l7 3v6c0 5-3.5 9.7-7 11-3.5-1.3-7-6-7-11V5l7-3zm-1 13l6-6-1.4-1.4L11 12.2 8.4 9.6 7 11l4 4z"/>
            </svg>
            Confiable
        </span>
    </div>
@endif

					
					<div class="container border rounded bg-body-tertiary p-4 p-lg-3 p-md-2">
						<div class="row gy-3">
							@include('front.account.partials.profile-photo')
							@include('front.account.partials.profile-details')
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('after_styles')
@endsection

@section('after_scripts')
@endsection
