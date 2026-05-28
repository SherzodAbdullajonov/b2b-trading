<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use App\DTOs\BatchRefund\BatchRefundInputDTO;
use App\DTOs\BatchRefund\BatchRefundLineDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreBatchRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refunded_at'           => ['required', 'date'],
            'reason'                => ['nullable', 'string', 'max:500'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.batch_item_id' => ['required', 'integer', 'exists:batch_items,id'],
            'items.*.qty'           => ['required', 'integer', 'min:1'],
        ];
    }

    public function toDTO(int $batchId): BatchRefundInputDTO
    {
        $data = $this->validated();

        $lines = array_map(
            fn (array $row): BatchRefundLineDTO => new BatchRefundLineDTO(
                batchItemId: (int) $row['batch_item_id'],
                qty:         (int) $row['qty'],
            ),
            $data['items'],
        );

        return new BatchRefundInputDTO(
            batchId:    $batchId,
            refundedAt: Carbon::parse((string) $data['refunded_at']),
            reason:     $data['reason'] ?? null,
            lines:      $lines,
        );
    }
}
