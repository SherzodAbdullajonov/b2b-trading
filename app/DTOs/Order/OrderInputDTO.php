<?php

declare(strict_types=1);

namespace App\DTOs\Order;

final class OrderInputDTO
{
    /**
     * @param  OrderLineRequestDTO[]  $lines
     */
    public function __construct(
        public readonly int   $clientId,
        public readonly array $lines,
    ) {
    }
}
