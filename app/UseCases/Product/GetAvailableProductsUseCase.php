<?php

declare(strict_types=1);

namespace App\UseCases\Product;

use App\DTOs\Product\AvailableProductDTO;
use App\Repositories\ProductRepository;

final class GetAvailableProductsUseCase
{
    public function __construct(
        private readonly ProductRepository $products,
    ) {
    }

    /**
     * @return AvailableProductDTO[]
     */
    public function execute(): array
    {
        $rows = $this->products->availableForOrdering();

        $byProduct = [];
        foreach ($rows as $row) {
            $available = (int) $row->qty
                - (int) $row->refunded
                - (int) $row->sold
                + (int) $row->returned;

            $pid = (int) $row->product_id;

            if (!isset($byProduct[$pid])) {
                $byProduct[$pid] = [
                    'id'            => $pid,
                    'name'          => (string) $row->product_name,
                    'category_name' => (string) $row->category_name,
                    'price'         => null,
                    'qty'           => 0,
                ];
            }

            $byProduct[$pid]['qty'] += $available;

            if ($byProduct[$pid]['price'] === null && $available > 0) {
                $byProduct[$pid]['price'] = (string) $row->sale_price;
            }
        }

        $result = [];
        foreach ($byProduct as $data) {
            if ($data['qty'] <= 0) {
                continue;
            }

            $result[] = new AvailableProductDTO(
                id:           $data['id'],
                name:         $data['name'],
                categoryName: $data['category_name'],
                price:        $data['price'] ?? '0.00',
                qty:          $data['qty'],
            );
        }

        return $result;
    }
}
