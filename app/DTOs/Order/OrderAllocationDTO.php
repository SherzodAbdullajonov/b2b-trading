<?php

declare(strict_types=1);

namespace App\DTOs\Order;

final class OrderAllocationDTO
{
    public function __construct(
        public readonly int    $productId,
        public readonly int    $batchId,
        public readonly int    $batchItemId,
        public readonly int    $qty,
        public readonly string $unitPrice,
    ) {
    }
}
