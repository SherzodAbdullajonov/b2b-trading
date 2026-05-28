<?php

declare(strict_types=1);

namespace App\UseCases\Purchase;

use App\DTOs\Purchase\PurchaseInputDTO;
use App\DTOs\Purchase\PurchaseResultDTO;
use App\Repositories\BatchRepository;
use App\Services\Category\CategoryProviderValidator;
use Illuminate\Support\Facades\DB;

final class CreatePurchaseUseCase
{
    public function __construct(
        private readonly CategoryProviderValidator $categoryValidator,
        private readonly BatchRepository           $batches,
    ) {
    }

    public function execute(PurchaseInputDTO $input): PurchaseResultDTO
    {
        $productIds = array_values(array_unique(
            array_map(fn ($line) => $line->productId, $input->lines)
        ));

        $this->categoryValidator->assertAllBelongTo($productIds, $input->providerId);

        $batch = DB::transaction(function () use ($input) {
            return $this->batches->createWithItems($input);
        });

        return new PurchaseResultDTO(
            batchId:      $batch->id,
            itemsCreated: count($input->lines),
        );
    }
}
