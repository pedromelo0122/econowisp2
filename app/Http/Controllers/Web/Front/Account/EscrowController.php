<?php
/*
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
 */

namespace App\Http\Controllers\Web\Front\Account;

use App\Helpers\Common\Num;
use App\Models\Currency;
use App\Models\EscrowTransaction;
use App\Services\UserService;
use Bedigit\Breadcrumbs\BreadcrumbFacade;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class EscrowController extends AccountBaseController
{
        /**
         * @param \App\Services\UserService $userService
         */
        public function __construct(UserService $userService)
        {
                parent::__construct($userService);
        }

        /**
         * Display escrow overview for the authenticated user.
         */
        public function index()
        {
                $user = auth()->user();
                $userId = $user->getAuthIdentifier();

                $transactions = EscrowTransaction::query()
                        ->with(['post', 'buyer', 'seller', 'currency'])
                        ->where(function (Builder $query) use ($userId) {
                                $query->where('buyer_id', $userId)
                                        ->orWhere('seller_id', $userId);
                        })
                        ->orderByDesc('created_at')
                        ->paginate(15);

                $totals = $this->buildSummary($userId);

                $title = t('escrow_transactions');
                MetaTag::set('title', $title . ' - ' . config('settings.app.name'));
                MetaTag::set('description', t('escrow_meta_description'));

                BreadcrumbFacade::add(t('escrow_transactions'));

                return view('front.account.escrow.index', [
                        'transactions' => $transactions,
                        'totals'       => $totals,
                        'userId'       => $userId,
                ]);
        }

        /**
         * Build summary totals grouped by currency for seller and buyer balances.
         */
        protected function buildSummary(int $userId): array
        {
                $onHoldSeller = $this->aggregateTotals(
                        EscrowTransaction::query()
                                ->onHold()
                                ->where('seller_id', $userId)
                );

                $releasedSeller = $this->aggregateTotals(
                        EscrowTransaction::query()
                                ->released()
                                ->where('seller_id', $userId)
                );

                $onHoldBuyer = $this->aggregateTotals(
                        EscrowTransaction::query()
                                ->onHold()
                                ->where('buyer_id', $userId)
                );

                return [
                        'seller_on_hold'   => $onHoldSeller,
                        'seller_released'  => $releasedSeller,
                        'buyer_on_hold'    => $onHoldBuyer,
                ];
        }

        /**
         * @param Builder $query
         * @return Collection<int, array{currency: string, formatted: string, total: float}>
         */
        protected function aggregateTotals(Builder $query): Collection
        {
                $results = $query
                        ->selectRaw('currency_code, SUM(amount) as total')
                        ->groupBy('currency_code')
                        ->get();

                if ($results->isEmpty()) {
                        return collect();
                }

                $currencyCodes = $results->pluck('currency_code')->filter()->unique();
                $currencies = Currency::query()
                        ->whereIn('code', $currencyCodes)
                        ->get()
                        ->keyBy('code');

                return $results->map(function ($row) use ($currencies) {
                        $currency = $currencies->get($row->currency_code);
                        $currencyArray = $currency?->toArray();
                        $formatted = Num::money($row->total, $currencyArray);
                        $defaultCurrency = data_get(config('currency', []), 'code', '--');

                        return [
                                'currency' => $row->currency_code ?? $defaultCurrency,
                                'formatted' => $formatted,
                                'total'     => (float) $row->total,
                        ];
                });
        }
}