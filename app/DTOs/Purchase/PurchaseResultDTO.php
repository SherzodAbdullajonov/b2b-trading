<?php

declare(strict_types=1);

namespace App\DTOs\Purchase;

/**
 * Output of CreatePurchaseUseCase — the newly created batch + how many lines it has.
 */
final class PurchaseResultDTO
{
    public function __construct(
        public readonly int $batchId,
        public readonly int $itemsCreated,
    ) {
    }
}
