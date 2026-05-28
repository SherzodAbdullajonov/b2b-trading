<?php

declare(strict_types=1);

namespace App\DTOs\Order;

final class OrderLineRequestDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $qty,
    ) {
    }
}
