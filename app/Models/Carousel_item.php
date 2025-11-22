<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carousel_item extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'order',
        'is_active'
    ];
}