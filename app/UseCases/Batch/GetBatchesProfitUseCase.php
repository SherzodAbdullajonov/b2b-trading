<?php

declare(strict_types=1);

namespace App\UseCases\Batch;

use App\DTOs\Batch\BatchProfitDTO;
use App\Repositories\BatchRepository;
use Illuminate\Support\Carbon;

final class GetBatchesProfitUseCase
{
    public function __construct(
        private readonly BatchRepository $batches,
    ) {
    }

    /**
     * @return BatchProfitDTO[]
     */
    public function execute(): array
    {
        return $this->batches->profitPerBatch()
            ->map(fn ($row) => new BatchProfitDTO(
                batchId:     (int) $row->batch_id,
                providerId:  (int) $row->provider_id,
                purchasedAt: Carbon::parse((string) $row->purchased_at)->format('Y-m-d H:i:s'),
                cost:        number_format((float) $row->cost,    2, '.', ''),
                revenue:     number_format((float) $row->revenue, 2, '.', ''),
                profit:      number_format((float) $row->profit,  2, '.', ''),
            ))
            ->all();
    }
}
