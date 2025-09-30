@php
    $class ??= '';
    $label ??= t('verified_seller');
@endphp

<span class="badge bg-success-subtle text-success-emphasis fw-normal {{ $class }}">
    <i class="fa-solid fa-circle-check me-1"></i> {{ $label }}
</span>