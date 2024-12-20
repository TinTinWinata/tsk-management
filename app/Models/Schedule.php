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
        'date',
        'assignee_id'
    ];

    public function assignee() {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function scheduleable()
    {
        return $this->morphTo();
    }

    protected $casts = [];
}
