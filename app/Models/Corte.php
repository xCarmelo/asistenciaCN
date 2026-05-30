<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corte extends Model
{
    /** @use HasFactory<\Database\Factories\CorteFactory> */
    use HasFactory;
    protected $table = 'cortes';
}
