<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use App\DTOs\Order\OrderInputDTO;
use App\DTOs\Order\OrderLineRequestDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id'        => ['required', 'integer', 'exists:clients,id'],
            'products'         => ['required', 'array', 'min:1'],
            'products.*.id'    => ['required', 'integer', 'exists:products,id'],
            'products.*.qty'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function toDTO(): OrderInputDTO
    {
        $data = $this->validated();

        $lines = array_map(
            fn (array $row): OrderLineRequestDTO => new OrderLineRequestDTO(
                productId: (int) $row['id'],
                qty:       (int) $row['qty'],
            ),
            $data['products'],
        );

        return new OrderInputDTO(
            clientId: (int) $data['client_id'],
            lines:    $lines,
        );
    }
}
