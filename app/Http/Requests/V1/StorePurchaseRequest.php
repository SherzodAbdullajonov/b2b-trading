<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use App\DTOs\Purchase\PurchaseInputDTO;
use App\DTOs\Purchase\PurchaseLineDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'provider_id'           => ['required', 'integer', 'exists:providers,id'],
            'storage_id'            => ['required', 'integer', 'exists:storages,id'],
            'purchased_at'          => ['required', 'date'],
            'note'                  => ['nullable', 'string', 'max:500'],

            'items'                 => ['required', 'array', 'min:1'],
            'items.*.product_id'    => ['required', 'integer', 'exists:products,id'],
            'items.*.qty'           => ['required', 'integer', 'min:1'],
            'items.*.unit_cost'     => ['required', 'numeric', 'min:0'],
            'items.*.sale_price'    => ['required', 'numeric', 'min:0', 'gte:items.*.unit_cost'],
        ];
    }

    /**
     * Build the immutable DTO from validated data. Controllers should call this
     * once and pass the result straight to the UseCase.
     */
    public function toDTO(): PurchaseInputDTO
    {
        /** @var array $data */
        $data = $this->validated();

        $lines = array_map(
            fn (array $row): PurchaseLineDTO => new PurchaseLineDTO(
                productId: (int) $row['product_id'],
                qty:       (int) $row['qty'],
                unitCost:  number_format((float) $row['unit_cost'], 2, '.', ''),
                salePrice: number_format((float) $row['sale_price'], 2, '.', ''),
            ),
            $data['items'],
        );

        return new PurchaseInputDTO(
            providerId:  (int) $data['provider_id'],
            storageId:   (int) $data['storage_id'],
            purchasedAt: Carbon::parse((string) $data['purchased_at']),
            note:        $data['note'] ?? null,
            lines:       $lines,
        );
    }
}
