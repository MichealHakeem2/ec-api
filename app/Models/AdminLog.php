<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;

    protected $table = 'adminlogs';

    protected $fillable = ['admin_id', 'action_type', 'details'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

