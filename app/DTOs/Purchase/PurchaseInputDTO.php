<?php

declare(strict_types=1);

namespace App\DTOs\Purchase;

use Illuminate\Support\Carbon;

/**
 * Input to CreatePurchaseUseCase. Built from a validated StorePurchaseRequest.
 *
 * @property-read PurchaseLineDTO[] $lines
 */
final class PurchaseInputDTO
{
    /**
     * @param  PurchaseLineDTO[]  $lines
     */
    public function __construct(
        public readonly int    $providerId,
        public readonly int    $storageId,
        public readonly Carbon $purchasedAt,
        public readonly ?string $note,
        public readonly array  $lines,
    ) {
    }
}
