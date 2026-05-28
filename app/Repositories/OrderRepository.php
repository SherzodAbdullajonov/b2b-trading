<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Order\OrderAllocationDTO;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;

final class OrderRepository
{
    /**
     * @param  OrderAllocationDTO[]  $allocations
     */
    public function create(int $clientId, array $allocations): Order
    {
        $order = Order::query()->create([
            'client_id'  => $clientId,
            'ordered_at' => Carbon::now(),
            'status'     => Order::STATUS_PLACED,
        ]);

        $now = Carbon::now();
        $rows = array_map(
            fn (OrderAllocationDTO $a): array => [
                'order_id'      => $order->id,
                'batch_item_id' => $a->batchItemId,
                'product_id'    => $a->productId,
                'qty'           => $a->qty,
                'unit_price'    => $a->unitPrice,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            $allocations,
        );

        OrderItem::query()->insert($rows);

        return $order;
    }
}
