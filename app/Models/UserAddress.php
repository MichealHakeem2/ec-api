<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'useraddresses';

    protected $fillable = ['user_id', 'address_line', 'city', 'postal_code', 'country', 'is_default'];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}
