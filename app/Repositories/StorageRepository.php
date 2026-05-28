<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class StorageRepository
{
    public function remainingAt(Carbon $asOf): Collection
    {
        $refunded = DB::table('batch_refund_items as bri')
            ->join('batch_refunds as br', 'br.id', '=', 'bri.batch_refund_id')
            ->where('br.refunded_at', '<=', $asOf)
            ->select('bri.batch_item_id', DB::raw('SUM(bri.qty) AS total'))
            ->groupBy('bri.batch_item_id');

        $sold = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->where('o.ordered_at', '<=', $asOf)
            ->select('oi.batch_item_id', DB::raw('SUM(oi.qty) AS total'))
            ->groupBy('oi.batch_item_id');

        $returned = DB::table('order_refund_items as ori')
            ->join('order_refunds as ord', 'ord.id', '=', 'ori.order_refund_id')
            ->join('order_items as oi2', 'oi2.id', '=', 'ori.order_item_id')
            ->where('ord.refunded_at', '<=', $asOf)
            ->select('oi2.batch_item_id', DB::raw('SUM(ori.qty) AS total'))
            ->groupBy('oi2.batch_item_id');

        $qtyExpression =
            'SUM(bi.qty '
            . '- COALESCE(refunded.total, 0) '
            . '- COALESCE(sold.total, 0) '
            . '+ COALESCE(returned.total, 0))';

        return DB::table('batch_items as bi')
            ->join('batches as b',  'b.id', '=', 'bi.batch_id')
            ->join('products as p', 'p.id', '=', 'bi.product_id')
            ->join('storages as s', 's.id', '=', 'b.storage_id')
            ->leftJoinSub($refunded, 'refunded', 'refunded.batch_item_id', '=', 'bi.id')
            ->leftJoinSub($sold,     'sold',     'sold.batch_item_id',     '=', 'bi.id')
            ->leftJoinSub($returned, 'returned', 'returned.batch_item_id', '=', 'bi.id')
            ->where('b.purchased_at', '<=', $asOf)
            ->groupBy('b.storage_id', 's.name', 'bi.product_id', 'p.name')
            ->havingRaw("{$qtyExpression} >= 0")
            ->orderBy('b.storage_id')
            ->orderBy('bi.product_id')
            ->select([
                'b.storage_id',
                's.name AS storage_name',
                'bi.product_id',
                'p.name AS product_name',
                DB::raw("{$qtyExpression} AS qty"),
            ])
            ->get();
    }
}
