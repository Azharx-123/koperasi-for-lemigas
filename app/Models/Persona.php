<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
  use HasFactory;

  protected $fillable = [
    'title',
    'subtitle',
    'description',
    'icon',
    'count',
    'order',
    'is_active'
  ];

  protected $casts = [
    'is_active' => 'boolean',
    'count' => 'integer',
    'order' => 'integer'
  ];
}
