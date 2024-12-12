<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = ['user_id', 'total_amount', 'status', 'delivery_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'order_id');
    }

    public function cartItems()
    {
        return $this->hasManyThrough(
            CartItem::class,
            Cart::class,
            'user_id', // Foreign key on the Cart model
            'cart_id', // Foreign key on the CartItem model
            'user_id', // Local key on the Order model
            'id' // Local key on the Cart model
        );
    }
}
