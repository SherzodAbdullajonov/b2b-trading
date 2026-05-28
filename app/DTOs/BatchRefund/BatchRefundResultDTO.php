<?php

declare(strict_types=1);

namespace App\DTOs\BatchRefund;

final class BatchRefundResultDTO
{
    public function __construct(
        public readonly int $batchRefundId,
        public readonly int $itemsRefunded,
    ) {
    }
}
