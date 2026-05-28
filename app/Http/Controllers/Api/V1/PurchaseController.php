<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StorePurchaseRequest;
use App\Http\Resources\V1\BatchResource;
use App\UseCases\Purchase\CreatePurchaseUseCase;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly CreatePurchaseUseCase $createPurchase,
    ) {
    }

    public function store(StorePurchaseRequest $request): JsonResponse
    {
        $result = $this->createPurchase->execute($request->toDTO());

        return BatchResource::make($result)
            ->additional(['message' => 'Purchase recorded.'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
