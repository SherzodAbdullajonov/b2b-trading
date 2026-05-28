<?php

declare(strict_types=1);

namespace App\DTOs\Storage;

final class RemainingStockDTO
{
    public function __construct(
        public readonly int    $storageId,
        public readonly string $storageName,
        public readonly int    $productId,
        public readonly string $productName,
        public readonly int    $qty,
    ) {
    }
}
