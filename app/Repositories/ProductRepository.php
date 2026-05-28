<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ProductRepository
{
    public function availableForOrdering(): Collection
    {
        $refunded = DB::table('batch_refund_items')
            ->select('batch_item_id', DB::raw('SUM(qty) AS refunded'))
            ->groupBy('batch_item_id');

        $sold = DB::table('order_items')
            ->select('batch_item_id', DB::raw('SUM(qty) AS sold'))
            ->groupBy('batch_item_id');

        $returned = DB::table('order_refund_items as ori')
            ->join('order_items as oi2', 'oi2.id', '=', 'ori.order_item_id')
            ->select('oi2.batch_item_id', DB::raw('SUM(ori.qty) AS returned'))
            ->groupBy('oi2.batch_item_id');

        return DB::table('batch_items as bi')
            ->join('batches as b', 'b.id', '=', 'bi.batch_id')
            ->join('products as p', 'p.id', '=', 'bi.product_id')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoinSub($refunded, 'br',  'br.batch_item_id', '=', 'bi.id')
            ->leftJoinSub($sold,     'soi', 'soi.batch_item_id', '=', 'bi.id')
            ->leftJoinSub($returned, 'ret', 'ret.batch_item_id', '=', 'bi.id')
            ->orderBy('p.id')
            ->orderBy('b.purchased_at')
            ->orderBy('bi.id')
            ->get([
                'p.id AS product_id',
                'p.name AS product_name',
                'c.name AS category_name',
                'bi.id AS batch_item_id',
                'bi.sale_price',
                'b.purchased_at',
                'bi.qty',
                DB::raw('COALESCE(br.refunded, 0) AS refunded'),
                DB::raw('COALESCE(soi.sold, 0)    AS sold'),
                DB::raw('COALESCE(ret.returned, 0) AS returned'),
            ]);
    }
}
