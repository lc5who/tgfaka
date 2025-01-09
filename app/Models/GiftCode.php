<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCode extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = [
        'product_id',
        'code',
        'used',
    ];
}
