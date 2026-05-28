<?php

declare(strict_types=1);

namespace App\DTOs\Purchase;

/**
 * One line in a purchase request: which product, how many, at what cost,
 * and what sale price the resulting batch_item will carry.
 */
final class PurchaseLineDTO
{
    public function __construct(
        public readonly int    $productId,
        public readonly int    $qty,
        public readonly string $unitCost,   // decimal as string to preserve precision
        public readonly string $salePrice,
    ) {
    }
}
