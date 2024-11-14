<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    public function users(){
        return $this->belongsTo(User::class);
    }

    protected $fillable = ['title', 'type', 'user_id', 'meta_id'];

    use HasFactory;
}