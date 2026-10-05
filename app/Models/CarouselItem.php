<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarouselItem extends Model
{
    use SoftDeletes;

    protected $table = 'carousel_items';

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];
}
