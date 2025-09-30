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

namespace App\Http\Controllers\Web\Front\Post;

use App\Enums\EscrowStatus;
use App\Http\Controllers\Web\Front\FrontController;
use App\Models\EscrowTransaction;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EscrowController extends FrontController
{
        /**
         * Create a new escrow transaction for the given listing.
         */
        public function store(string $hashableId): RedirectResponse
        {
                abort_unless(auth()->check(), 403);

                $postId = hashId($hashableId, true) ?? $hashableId;

                $post = Post::query()
                        ->select(['id', 'price', 'currency_code', 'user_id', 'title'])
                        ->find($postId);

                if (empty($post)) {
                        flash(t('post_not_found'))->error();

                        return redirect()->back();
                }

                $buyer = auth()->user();

                if ($post->user_id === $buyer->getAuthIdentifier()) {
                        flash(t('escrow_transaction_error_owner'))->warning();

                        return redirect()->back();
                }

                if (empty($post->price) || $post->price <= 0) {
                        flash(t('escrow_transaction_invalid_amount'))->warning();

                        return redirect()->back();
                }

                $activeStatuses = EscrowStatus::openStatusesValues();

                $existing = EscrowTransaction::query()
                        ->where('post_id', $post->getKey())
                        ->where('buyer_id', $buyer->getAuthIdentifier())
                        ->whereIn('status', $activeStatuses)
                        ->first();

                if ($existing) {
                        flash(t('escrow_transaction_exists'))->warning();

                        return redirect()->route('account.escrow');
                }

                try {
                        DB::transaction(function () use ($post, $buyer) {
                                EscrowTransaction::create([
                                        'post_id'       => $post->getKey(),
                                        'buyer_id'      => $buyer->getAuthIdentifier(),
                                        'seller_id'     => $post->user_id,
                                        'amount'        => $post->price,
                                        'currency_code' => $post->currency_code,
                                        'status'        => EscrowStatus::Pending->value,
                                ]);
                        });
                } catch (\Throwable $e) {
                        Log::error('Failed to create escrow transaction', [
                                'post_id' => $post->getKey(),
                                'buyer_id' => $buyer->getAuthIdentifier(),
                                'message' => $e->getMessage(),
                        ]);

                        flash(t('escrow_transaction_error'))->error();

                        return redirect()->back();
                }

                flash(t('escrow_transaction_created'))->success();

                return redirect()->route('account.escrow');
        }
}