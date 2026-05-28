<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreBatchRefundRequest;
use App\Http\Resources\V1\BatchRefundResource;
use App\Models\Batch;
use App\UseCases\BatchRefund\CreateBatchRefundUseCase;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BatchRefundController extends Controller
{
    public function __construct(
        private readonly CreateBatchRefundUseCase $createBatchRefund,
    ) {
    }

    public function store(StoreBatchRefundRequest $request, Batch $batch): JsonResponse
    {
        $result = $this->createBatchRefund->execute($request->toDTO($batch->id));

        return BatchRefundResource::make($result)
            ->additional(['message' => 'Batch refund recorded.'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
