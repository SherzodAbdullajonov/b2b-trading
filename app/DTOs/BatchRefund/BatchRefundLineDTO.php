<?php

declare(strict_types=1);

namespace App\DTOs\BatchRefund;

final class BatchRefundLineDTO
{
    public function __construct(
        public readonly int $batchItemId,
        public readonly int $qty,
    ) {
    }
}
