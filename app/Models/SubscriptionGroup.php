<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'product_subscription_id',
        'max_capacity',
        'participant_count',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productSubscription(): BelongsTo
    {
        return $this->belongsTo(ProductSubscription::class, 'product_subscription_id');
    }

    public function groupMessages(): HasMany
    {
        return $this->hasMany(GroupMessage::class)->latest();
    }

    public function groupParticipants(): HasMany
    {
        return $this->hasMany(GroupParticipant::class);
    }
}
