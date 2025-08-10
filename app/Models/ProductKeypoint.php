<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HaFactory;
use Illuminate\Database\Eloquent\Relations\SoftDeletes;


class ProductKeypoint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'product_id',
    ];
}
