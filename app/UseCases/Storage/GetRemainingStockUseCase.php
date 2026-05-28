<?php

declare(strict_types=1);

namespace App\UseCases\Storage;

use App\DTOs\Storage\RemainingStockDTO;
use App\Repositories\StorageRepository;
use Illuminate\Support\Carbon;

final class GetRemainingStockUseCase
{
    public function __construct(
        private readonly StorageRepository $storages,
    ) {
    }

    /**
     * @return RemainingStockDTO[]
     */
    public function execute(Carbon $asOf): array
    {
        return $this->storages->remainingAt($asOf)
            ->map(fn ($row) => new RemainingStockDTO(
                storageId:   (int) $row->storage_id,
                storageName: (string) $row->storage_name,
                productId:   (int) $row->product_id,
                productName: (string) $row->product_name,
                qty:         (int) $row->qty,
            ))
            ->all();
    }
}
