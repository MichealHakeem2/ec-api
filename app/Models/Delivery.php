<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'delivery';

    protected $fillable = ['order_id', 'delivery_date', 'delivery_address', 'status'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
