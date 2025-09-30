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

namespace App\Models;

use App\Enums\BootstrapColor;
use App\Enums\EscrowStatus;
use App\Helpers\Common\Num;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud; // ← LINEA NUEVA

class EscrowTransaction extends BaseModel
{
    use Crud; // ← LINEA NUEVA

        /**
         * The table associated with the model.
         *
         * @var string
         */
        protected $table = 'escrow_transactions';

        /**
         * @var array<int, string>
         */
        protected $fillable = [
                'reference',
                'post_id',
                'buyer_id',
                'seller_id',
                'amount',
                'currency_code',
                'status',
                'hold_until',
                'released_at',
                'cancelled_at',
                'notes',
        ];

        /**
         * @var array<int, string>
         */
        protected $appends = [
                'status_label',
                'status_badge_class',
                'amount_formatted',
        ];

        /**
         * @return array<string, string>
         */
        protected function casts(): array
        {
                return [
                        'amount'       => 'decimal:2',
                        'hold_until'   => 'datetime',
                        'released_at'  => 'datetime',
                        'cancelled_at' => 'datetime',
                ];
        }

        /**
         * Boot the model and handle events.
         */
        protected static function booted(): void
        {
                static::creating(function (self $transaction) {
                        if (empty($transaction->reference)) {
                                $transaction->reference = self::generateReference();
                        }

                        if (empty($transaction->status)) {
                                $transaction->status = EscrowStatus::Pending->value;
                        }
                });

                static::saving(function (self $transaction) {
                        if ($transaction->isDirty('status')) {
                                $status = EscrowStatus::tryFrom($transaction->status);

                                if ($status === EscrowStatus::Released) {
                                        $transaction->released_at = now();
                                        $transaction->cancelled_at = null;
                                }

                                if ($status === EscrowStatus::Cancelled) {
                                        $transaction->cancelled_at = now();
                                        $transaction->released_at = null;
                                }
                        }
                });
        }

        /*
        |--------------------------------------------------------------------------
        | RELATIONS
        |--------------------------------------------------------------------------
        */
        public function post(): BelongsTo
        {
                return $this->belongsTo(Post::class);
        }

        public function buyer(): BelongsTo
        {
                return $this->belongsTo(User::class, 'buyer_id');
        }

        public function seller(): BelongsTo
        {
                return $this->belongsTo(User::class, 'seller_id');
        }

        public function currency(): BelongsTo
        {
                return $this->belongsTo(Currency::class, 'currency_code', 'code');
        }

        /*
        |--------------------------------------------------------------------------
        | ACCESSORS
        |--------------------------------------------------------------------------
        */
        protected function statusLabel(): Attribute
        {
                return Attribute::make(
                        get: fn (): string => EscrowStatus::tryFrom($this->status)?->label() ?? $this->status
                );
        }

        protected function statusBadgeClass(): Attribute
        {
                return Attribute::make(
                        get: function (): string {
                                $status = EscrowStatus::tryFrom($this->status);
                                $color = $status?->color() ?? 'secondary';

                                return BootstrapColor::Badge->getColorClass($color);
                        }
                );
        }

        protected function amountFormatted(): Attribute
        {
                return Attribute::make(
                        get: function (): string {
                                $currency = $this->currency?->toArray();

                                return Num::money($this->amount, $currency);
                        }
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SCOPES
        |--------------------------------------------------------------------------
        */
        public function scopeOpen($query)
        {
                return $query->whereIn('status', EscrowStatus::openStatusesValues());
        }

        public function scopeOnHold($query)
        {
                return $query->whereIn('status', EscrowStatus::onHoldStatusesValues());
        }

        public function scopeReleased($query)
        {
                return $query->where('status', EscrowStatus::Released->value);
        }

        /*
        |--------------------------------------------------------------------------
        | OTHER METHODS
        |--------------------------------------------------------------------------
        */
        public static function generateReference(): string
        {
                do {
                        $reference = Str::upper(Str::random(12));
                } while (self::query()->where('reference', $reference)->exists());

                return $reference;
        }

        /**
         * @return array<string, string>
         */
        public static function statusOptions(): array
        {
                return EscrowStatus::labels();
        }

        public function statusBadge(): string
        {
                $class = $this->status_badge_class;
                $label = e($this->status_label);

                return '<span class="badge ' . $class . '">' . $label . '</span>';
        }

        public function crudListingColumn(): string
        {
                if (!$this->post) {
                        return '--';
                }

                $title = e($this->post->title);
                $adminUrl = urlGen()->adminUrl('posts/' . $this->post->getKey() . '/edit');
                $frontUrl = urlGen()->post($this->post);

                return '<a href="' . $adminUrl . '" class="text-decoration-none">' . $title . '</a>'
                        . '<br><a href="' . $frontUrl . '" class="text-decoration-none" target="_blank">'
                        . '<i class="fa-solid fa-arrow-up-right-from-square"></i> ' . trans('admin.preview') . '</a>';
        }

        public function crudBuyerColumn(): string
        {
                if (!$this->buyer) {
                        return '--';
                }

                $name = e($this->buyer->name ?? $this->buyer->email);
                $adminUrl = urlGen()->adminUrl('users/' . $this->buyer->getAuthIdentifier() . '/edit');

                return '<a href="' . $adminUrl . '" class="text-decoration-none">' . $name . '</a>';
        }

        public function crudSellerColumn(): string
        {
                if (!$this->seller) {
                        return '--';
                }

                $name = e($this->seller->name ?? $this->seller->email);
                $adminUrl = urlGen()->adminUrl('users/' . $this->seller->getAuthIdentifier() . '/edit');

                return '<a href="' . $adminUrl . '" class="text-decoration-none">' . $name . '</a>';
        }

        public function crudAmountColumn(): string
        {
                $currency = $this->currency?->toArray();

                return Num::money($this->amount, $currency);
        }

        public function crudStatusColumn(): string
        {
                return $this->statusBadge();
        }

        /**
         * @param int $userId
         * @return string
         */
        public function roleLabelForUser(int $userId): string
        {
                if ($this->seller_id === $userId) {
                        return trans('global.escrow_role_seller');
                }

                if ($this->buyer_id === $userId) {
                        return trans('global.escrow_role_buyer');
                }

                return trans('global.user');
        }

        /**
         * @return array<int, string>
         */
        public static function badgeClasses(): array
        {
                return collect(EscrowStatus::cases())
                        ->mapWithKeys(fn (EscrowStatus $status) => [$status->value => BootstrapColor::Badge->getColorClass($status->color())])
                        ->toArray();
        }
}