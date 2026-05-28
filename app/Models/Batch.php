<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = ['provider_id', 'storage_id', 'purchased_at', 'note'];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }

    /** Line items: products purchased in this batch. */
    public function items(): HasMany
    {
        return $this->hasMany(BatchItem::class);
    }

    /** Refund events for this batch (back to provider). */
    public function refunds(): HasMany
    {
        return $this->hasMany(BatchRefund::class);
    }
}
