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

    //relasi -> group milik product mana
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    //relasi -> group milik product subscription mana
    public function productSubscription(): BelongsTo
    {
        return $this->belongsTo(ProductSubscription::class, 'product_subscription_id');
    }

    //relasi -> group memiliki banyak message
    public function groupMessage(): HasMany
    {
        return $this->hasMany(GroubMessage::class)->latest();
    }

    //relasi -> group memiliki banyak partisipan
    public function groupParticipant():HasMany
    {
        return $this->hasMany(GroubParticipant::class);
    }
}
