@php
	use Illuminate\Support\Collection;
	
	$flashNotification = session('flash_notification', collect());
	$flashNotification = ($flashNotification instanceof Collection) ? $flashNotification : collect();
@endphp
@if ($flashNotification->isNotEmpty())
	@php
		$positionList = [
			'topLeft'      => 'top-0 start-0',
			'topCenter'    => 'top-0 start-50 translate-middle-x',
			'topRight'     => 'top-0 end-0',
			'middleLeft'   => 'top-50 start-0 translate-middle-y',
			'middleCenter' => 'top-50 start-50 translate-middle',
			'middleRight'  => 'top-50 end-0 translate-middle-y',
			'bottomLeft'   => 'bottom-0 start-0',
			'bottomCenter' => 'bottom-0 start-50 translate-middle-x',
			'bottomRight'  => 'bottom-0 end-0',
		];
		
		$defaultPosition = 'bottomRight';
		$position = config('larapen.core.bsToast.position') ?? $defaultPosition;
		$position = array_key_exists($position, $positionList) ? $position : $defaultPosition;
		
		$placement = $positionList[$position];
		$animation = config('larapen.core.bsToast.animation') ?? 'true';
		$autohide = config('larapen.core.bsToast.autohide') ?? 'false';
		$delay = (int)(config('larapen.core.bsToast.delay') ?? 10000);
	@endphp
	@foreach ($flashNotification->toArray() as $message)
		@php
			$overlay = $message['overlay'] ?? false;
			$level = $message['level'] ?? 'light';
			$important = $message['important'] ?? '';
			$messageTitle = $message['title'] ?? '';
			$messageBody = $message['message'] ?? '';
		@endphp
		
		@if ($overlay)
			@include('flash::modal', [
				'modalClass' => 'flash-modal',
				'title'      => $messageTitle,
				'body'       => $messageBody,
			])
		@else
			
			<div class="toast-container position-fixed p-3 {{ $placement }}">
				<div class="toast align-items-center text-bg-{{ $level }} border-0" role="alert" aria-live="assertive" aria-atomic="true">
					<div class="d-flex">
						<div class="toast-body">
							{!! $messageBody !!}
						</div>
						<button
								type="button"
								class="btn-close btn-close-white me-2 m-auto"
								data-bs-dismiss="toast"
								aria-label="{{ t('Close') }}"
						></button>
					</div>
				</div>
			</div>
			
		@endif
	@endforeach
@endif

@section('after_scripts')
	@parent
	@if ($flashNotification->isNotEmpty())
		@php
			$animation ??= 'true';
			$autohide ??= 'false';
			$delay ??= 10000;
		@endphp
		<script>
			onDocumentReady((event) => {
				const toastEl = document.querySelector('.toast');
				if (toastEl) {
					const config = {
						animation: {{ $animation }},
						autohide: {{ $autohide }}
					};
					@if ($autohide === 'true')
						config.delay = {{ $delay }};
					@endif
					const toastMessage = new bootstrap.Toast(toastEl, config);
					toastMessage.show();
				}
			});
		</script>
	@endif
@endsection

@php
	session()->forget('flash_notification')
@endphp
