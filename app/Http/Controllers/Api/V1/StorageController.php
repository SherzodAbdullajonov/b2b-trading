<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\RemainingStockRequest;
use App\Http\Resources\V1\RemainingStockResource;
use App\UseCases\Storage\GetRemainingStockUseCase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StorageController extends Controller
{
    public function __construct(
        private readonly GetRemainingStockUseCase $getRemaining,
    ) {
    }

    public function remaining(RemainingStockRequest $request): AnonymousResourceCollection
    {
        return RemainingStockResource::collection(
            $this->getRemaining->execute($request->asOfDate())
        );
    }
}
