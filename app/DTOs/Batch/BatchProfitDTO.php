<?php

declare(strict_types=1);

namespace App\DTOs\Batch;

final class BatchProfitDTO
{
    public function __construct(
        public readonly int    $batchId,
        public readonly int    $providerId,
        public readonly string $purchasedAt,
        public readonly string $cost,
        public readonly string $revenue,
        public readonly string $profit,
    ) {
    }
}
