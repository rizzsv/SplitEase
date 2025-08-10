<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'price_per_person',
        'duration',
        'capacity',
        'is_popular',   
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

    public function groups(): HashMany
    {
        return $this->hashMany(SubscriptionGroup::class);
    }

    public function keypoints(): HashMany 
    {
        return $this->hashMany(ProductKeypoint::class);
    }
}
