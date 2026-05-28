<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PLACED     = 'placed';
    public const STATUS_FULFILLED  = 'fulfilled';
    public const STATUS_CANCELLED  = 'cancelled';

    protected $fillable = ['client_id', 'ordered_at', 'status'];

    protected $casts = [
        'ordered_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** Line items, possibly multiple per requested product (FIFO can split across batches). */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class);
    }
}
