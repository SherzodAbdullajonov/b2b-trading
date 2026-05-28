<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AvailableProductResource;
use App\UseCases\Product\GetAvailableProductsUseCase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly GetAvailableProductsUseCase $getAvailable,
    ) {
    }

    public function available(): AnonymousResourceCollection
    {
        return AvailableProductResource::collection($this->getAvailable->execute());
    }
}
