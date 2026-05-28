<?php

declare(strict_types=1);

namespace App\DTOs\Order;

final class OrderResultDTO
{
    /**
     * @param  OrderAllocationDTO[]  $allocations
     */
    public function __construct(
        public readonly int   $orderId,
        public readonly array $allocations,
    ) {
    }
}
