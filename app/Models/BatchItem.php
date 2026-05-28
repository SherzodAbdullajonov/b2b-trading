<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A single line in a single purchase batch. The atomic unit of cost accounting.
 * FIFO consumes batch_items in order of their batch's purchased_at.
 */
class BatchItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'product_id',
        'qty',
        'unit_cost',
        'sale_price',
    ];

    protected $casts = [
        'qty'        => 'integer',
        'unit_cost'  => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Units of this batch_item that have been refunded back to provider. */
    public function refundItems(): HasMany
    {
        return $this->hasMany(BatchRefundItem::class);
    }

    /** Order lines that drew units from this batch_item. */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
