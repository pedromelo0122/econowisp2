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
@php use App\Enums\EscrowStatus; use App\Enums\BootstrapColor; @endphp
@extends('front.layouts.master')

@php
        $totals ??= [];
        $transactions ??= collect();
        $userId ??= auth()->id();
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
                                        <div class="container border rounded bg-body-tertiary p-4 p-lg-3 p-md-2">
                                                <h3 class="fw-bold border-bottom pb-3 mb-4">
                                                        <i class="bi bi-shield-check"></i> {{ t('escrow_transactions') }}
                                                </h3>

                                                <div class="row g-3 mb-4">
                                                        <div class="col-md-4">
                                                                <div class="card h-100 border-success">
                                                                        <div class="card-body">
                                                                                <h6 class="card-subtitle mb-2 text-success text-uppercase">{{ t('escrow_balance_on_hold') }}</h6>
                                                                                @forelse(data_get($totals, 'seller_on_hold', collect()) as $row)
                                                                                        <div>{{ $row['formatted'] }}</div>
                                                                                @empty
                                                                                        <div class="text-secondary">{{ t('escrow_no_balance') }}</div>
                                                                                @endforelse
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                                <div class="card h-100 border-primary">
                                                                        <div class="card-body">
                                                                                <h6 class="card-subtitle mb-2 text-primary text-uppercase">{{ t('escrow_balance_released') }}</h6>
                                                                                @forelse(data_get($totals, 'seller_released', collect()) as $row)
                                                                                        <div>{{ $row['formatted'] }}</div>
                                                                                @empty
                                                                                        <div class="text-secondary">{{ t('escrow_no_balance') }}</div>
                                                                                @endforelse
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                                <div class="card h-100 border-warning">
                                                                        <div class="card-body">
                                                                                <h6 class="card-subtitle mb-2 text-warning text-uppercase">{{ t('escrow_balance_purchases') }}</h6>
                                                                                @forelse(data_get($totals, 'buyer_on_hold', collect()) as $row)
                                                                                        <div>{{ $row['formatted'] }}</div>
                                                                                @empty
                                                                                        <div class="text-secondary">{{ t('escrow_no_balance') }}</div>
                                                                                @endforelse
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>

                                                <div class="table-responsive">
                                                        <table class="table mb-0 table-striped align-middle">
                                                                <thead>
                                                                <tr>
                                                                        <th scope="col">{{ t('reference') }}</th>
                                                                        <th scope="col">{{ t('Listing') }}</th>
                                                                        <th scope="col">{{ t('escrow_role') }}</th>
                                                                        <th scope="col">{{ t('counterparty') }}</th>
                                                                        <th scope="col">{{ t('amount') }}</th>
                                                                        <th scope="col">{{ t('Status') }}</th>
                                                                        <th scope="col">{{ t('Date') }}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @forelse($transactions as $transaction)
                                                                        @php
                                                                                $isSeller = $transaction->seller_id === $userId;
                                                                                $roleLabel = $transaction->roleLabelForUser($userId);
                                                                                $counterparty = $isSeller ? $transaction->buyer : $transaction->seller;
                                                                                $counterpartyName = $counterparty->name ?? $counterparty->email ?? '--';
                                                                                $status = EscrowStatus::tryFrom($transaction->status);
                                                                                $badgeColor = $status?->color() ?? 'secondary';
                                                                                $badgeClass = BootstrapColor::Badge->getColorClass($badgeColor);
                                                                        @endphp
                                                                        <tr>
                                                                                <td>{{ $transaction->reference }}</td>
                                                                                <td>
                                                                                        @if ($transaction->post)
                                                                                                <a href="{{ urlGen()->post($transaction->post) }}" class="{{ linkClass() }}" target="_blank">
                                                                                                        {{ str($transaction->post->title)->limit(50) }}
                                                                                                </a>
                                                                                        @else
                                                                                                --
                                                                                        @endif
                                                                                </td>
                                                                                <td>{{ $roleLabel }}</td>
                                                                                <td>{{ $counterpartyName }}</td>
                                                                                <td>{!! $transaction->amount_formatted !!}</td>
                                                                                <td>
                                                                                        <span class="badge {{ $badgeClass }}">{{ $transaction->status_label }}</span>
                                                                                </td>
                                                                                <td>{!! $transaction->created_at?->translatedFormat('Y-m-d H:i') !!}</td>

                                                                        </tr>
                                                                @empty
                                                                        <tr>
                                                                                <td colspan="7">
                                                                                        <div class="text-center my-5">{{ t('escrow_transactions_empty') }}</div>
                                                                                </td>
                                                                        </tr>
                                                                @endforelse
                                                                </tbody>
                                                        </table>
                                                </div>

                                                @if (method_exists($transactions, 'links'))
                                                        <div class="mt-3">
                                                                {{ $transactions->links('vendor.pagination.bootstrap-5') }}
                                                        </div>
                                                @endif
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
@endsection

@section('after_scripts')
@endsection