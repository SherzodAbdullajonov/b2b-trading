<?php

declare(strict_types=1);

namespace App\UseCases\BatchRefund;

use App\DTOs\BatchRefund\BatchRefundInputDTO;
use App\DTOs\BatchRefund\BatchRefundResultDTO;
use App\Models\BatchItem;
use App\Repositories\BatchRefundRepository;
use App\Services\Batch\BatchItemAvailabilityCalculator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateBatchRefundUseCase
{
    public function __construct(
        private readonly BatchItemAvailabilityCalculator $availability,
        private readonly BatchRefundRepository           $repository,
    ) {
    }

    public function execute(BatchRefundInputDTO $input): BatchRefundResultDTO
    {
        $batchItemIds = array_values(array_unique(
            array_map(fn ($line) => $line->batchItemId, $input->lines)
        ));

        $requested = [];
        foreach ($input->lines as $line) {
            $requested[$line->batchItemId] = ($requested[$line->batchItemId] ?? 0) + $line->qty;
        }

        return DB::transaction(function () use ($input, $batchItemIds, $requested): BatchRefundResultDTO {
            $ownership = BatchItem::query()
                ->whereIn('id', $batchItemIds)
                ->pluck('batch_id', 'id')
                ->all();

            $foreign = [];
            foreach ($batchItemIds as $id) {
                $owningBatchId = $ownership[$id] ?? null;
                if ($owningBatchId !== $input->batchId) {
                    $foreign["items.{$id}.batch_item_id"] =
                        "Batch item {$id} does not belong to batch {$input->batchId}.";
                }
            }
            if ($foreign !== []) {
                throw ValidationException::withMessages($foreign);
            }

            $available = $this->availability->availableForIds($batchItemIds, lockForUpdate: true);

            $overflows = [];
            foreach ($requested as $itemId => $qty) {
                $unsold = $available[$itemId] ?? 0;
                if ($qty > $unsold) {
                    $overflows["items.{$itemId}.qty"] =
                        "Cannot refund {$qty} of batch_item {$itemId}; only {$unsold} unsold remaining.";
                }
            }
            if ($overflows !== []) {
                throw ValidationException::withMessages($overflows);
            }

            $refund = $this->repository->createWithItems($input);

            return new BatchRefundResultDTO(
                batchRefundId: $refund->id,
                itemsRefunded: count($input->lines),
            );
        });
    }
}
