<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'is_done',
        'position',
        'user_id',
        'date'
    ];

    public function scheduleable()
    {
        return $this->morphTo();
    }

    protected $casts = [];
}
