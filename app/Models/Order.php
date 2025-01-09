<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    use Notifiable;
    //
    protected  $guarded=[];


    public function giftCode()
    {
        return $this->hasMany(GiftCode::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
