<?php

declare(strict_types=1);

namespace App\DTOs\Product;

final class AvailableProductDTO
{
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $categoryName,
        public readonly string $price,
        public readonly int    $qty,
    ) {
    }
}
