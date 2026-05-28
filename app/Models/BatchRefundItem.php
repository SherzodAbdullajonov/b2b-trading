<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchRefundItem extends Model
{
    use HasFactory;

    protected $fillable = ['batch_refund_id', 'batch_item_id', 'qty'];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function refund(): BelongsTo
    {
        return $this->belongsTo(BatchRefund::class, 'batch_refund_id');
    }

    public function batchItem(): BelongsTo
    {
        return $this->belongsTo(BatchItem::class);
    }
}
