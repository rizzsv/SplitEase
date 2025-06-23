<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'thumbnail',
        'photo',
        'about',
        'tagline',
        'price',
        'duration',
        'capacity',
        'is_popular',
        'price_per_person',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => [
                'name' => $value,
                'slug' => Str::slug($value),
            ]
        );
    }

    //relasi -> product memiliki banyak group
    public function groups(): HasMany
    {
        return $this->hasMany(SubscriptionGroup::class);
    }

    //relasi -> product memiliki banyak subscription
    public function subscriptions(): HasMany
    {
        return $this->hasMany(ProductKeypoint::class);
    }
}
