<?php

declare(strict_types=1);

namespace App\UseCases\Order;

use App\DTOs\Order\OrderInputDTO;
use App\DTOs\Order\OrderResultDTO;
use App\Repositories\OrderRepository;
use App\Services\Order\FifoBatchAllocator;
use Illuminate\Support\Facades\DB;

final class CreateOrderUseCase
{
    public function __construct(
        private readonly FifoBatchAllocator $allocator,
        private readonly OrderRepository    $repository,
    ) {
    }

    public function execute(OrderInputDTO $input): OrderResultDTO
    {
        $needs = $this->collapseLines($input);

        return DB::transaction(function () use ($input, $needs): OrderResultDTO {
            $allocations = $this->allocator->allocate($needs);
            $order       = $this->repository->create($input->clientId, $allocations);

            return new OrderResultDTO(
                orderId:     $order->id,
                allocations: $allocations,
            );
        });
    }

    /**
     * @return array<int, int>
     */
    private function collapseLines(OrderInputDTO $input): array
    {
        $needs = [];
        foreach ($input->lines as $line) {
            $needs[$line->productId] = ($needs[$line->productId] ?? 0) + $line->qty;
        }
        return $needs;
    }
}
