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

namespace App\Enums;

enum EscrowStatus: string
{
        case Pending = 'pending';
        case Funded = 'funded';
        case Released = 'released';
        case Cancelled = 'cancelled';
        case Disputed = 'disputed';

        /**
         * @return string
         */
        public function label(): string
        {
                return trans('global.escrow_status_' . $this->value);
        }

        /**
         * @return string
         */
        public function color(): string
        {
                return match ($this) {
                        self::Pending  => 'warning',
                        self::Funded   => 'info',
                        self::Released => 'success',
                        self::Cancelled => 'secondary',
                        self::Disputed => 'danger',
                };
        }

        /**
         * @return array<string, string>
         */
        public static function labels(): array
        {
                return collect(self::cases())
                        ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
                        ->toArray();
        }

        /**
         * @return array<int, string>
         */
        public static function values(): array
        {
                return collect(self::cases())->map(fn (self $case) => $case->value)->toArray();
        }

        /**
         * @return array<int, string>
         */
        public static function onHoldStatusesValues(): array
        {
                return [
                        self::Pending->value,
                        self::Funded->value,
                        self::Disputed->value,
                ];
        }

        /**
         * @return array<int, string>
         */
        public static function openStatusesValues(): array
        {
                return [
                        self::Pending->value,
                        self::Funded->value,
                        self::Disputed->value,
                ];
        }
}