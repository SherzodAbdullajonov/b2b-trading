<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One line on a client order — pinned to a single batch_item via FIFO allocation.
 * If a requested product spans multiple batches, the order has multiple
 * order_items (one per batch).
 *
 * unit_price is a snapshot of batch_items.sale_price at order time so that
 * later edits to the batch's sale_price don't move historical revenue.
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'batch_item_id',
        'product_id',
        'qty',
        'unit_price',
    ];

    protected $casts = [
        'qty'        => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function batchItem(): BelongsTo
    {
        return $this->belongsTo(BatchItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Units of this order_item that have been refunded back from the client. */
    public function refundItems(): HasMany
    {
        return $this->hasMany(OrderRefundItem::class);
    }
}
