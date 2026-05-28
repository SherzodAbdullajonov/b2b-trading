<?php

declare(strict_types=1);

namespace App\Services\Category;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

final class CategoryProviderValidator
{
    /**
     * Throws ValidationException (HTTP 422) listing each offending product line.
     *
     * @param  int[]  $productIds  unique list of product ids being purchased
     * @param  int    $providerId  the provider of the batch
     */
    public function assertAllBelongTo(array $productIds, int $providerId): void
    {
        if ($productIds === []) {
            return;
        }

        // 1. Load products with their categories — one query.
        $products = Product::query()
            ->with('category')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id')
            ->all();

        // 2. Collect every category that appears in this purchase
        $categories = $this->loadCategoryTree($products);

        // 3. For each product, walk to the root and compare provider_id.
        $errors = [];
        foreach ($productIds as $productId) {
            if (!isset($products[$productId])) {
                $errors["items.{$productId}.product_id"] = "Product {$productId} not found.";
                continue;
            }

            $rootProviderId = $this->resolveRootProviderId($products[$productId], $categories);

            if ($rootProviderId !== $providerId) {
                $errors["items.{$productId}.product_id"] =
                    "Product {$productId} belongs to provider {$rootProviderId}, "
                    . "not provider {$providerId}.";
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function loadCategoryTree(array $products): array
    {
        $categories = [];
        $idsToLoad = [];

        foreach ($products as $product) {
            if ($product->category !== null) {
                $categories[$product->category->id] = $product->category;
                if ($product->category->parent_id !== null) {
                    $idsToLoad[$product->category->parent_id] = true;
                }
            }
        }

        // Walk up generation by generation. Worst case: tree depth iterations.
        while ($idsToLoad !== []) {
            $needed = array_keys(array_diff_key($idsToLoad, $categories));
            $idsToLoad = [];

            if ($needed === []) {
                break;
            }

            $parents = Category::query()
                ->whereIn('id', $needed)
                ->get();

            foreach ($parents as $parent) {
                $categories[$parent->id] = $parent;
                if ($parent->parent_id !== null && !isset($categories[$parent->parent_id])) {
                    $idsToLoad[$parent->parent_id] = true;
                }
            }
        }

        return $categories;
    }

    /**
     * @param  array<int, Category>  $categories indexed by id
     */
    private function resolveRootProviderId(Product $product, array $categories): ?int
    {
        $node = $product->category;

        while ($node?->parent_id !== null) {
            $node = $categories[$node->parent_id] ?? null;
            if ($node === null) {
                return null;
            }
        }

        return $node?->provider_id;
    }
}
