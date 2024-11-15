<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'owner_id'
    ];

    public function schedules() {
        return $this->morphMany(Schedule::class, 'scheduleable');
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }

    protected $casts = [];
}
