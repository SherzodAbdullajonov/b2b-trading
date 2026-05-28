<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BatchProfitResource;
use App\UseCases\Batch\GetBatchesProfitUseCase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BatchController extends Controller
{
    public function __construct(
        private readonly GetBatchesProfitUseCase $getProfit,
    ) {
    }

    public function profit(): AnonymousResourceCollection
    {
        return BatchProfitResource::collection($this->getProfit->execute());
    }
}
