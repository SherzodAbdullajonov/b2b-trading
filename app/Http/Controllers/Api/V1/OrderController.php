<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreOrderRequest;
use App\Http\Resources\V1\OrderResource;
use App\UseCases\Order\CreateOrderUseCase;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly CreateOrderUseCase $createOrder,
    ) {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $result = $this->createOrder->execute($request->toDTO());

        return OrderResource::make($result)
            ->additional(['message' => 'Order placed.'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
