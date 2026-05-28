<?php

declare(strict_types=1);

namespace App\DTOs\BatchRefund;

use Illuminate\Support\Carbon;

final class BatchRefundInputDTO
{
    /**
     * @param  BatchRefundLineDTO[]  $lines
     */
    public function __construct(
        public readonly int     $batchId,
        public readonly Carbon  $refundedAt,
        public readonly ?string $reason,
        public readonly array   $lines,
    ) {
    }
}
