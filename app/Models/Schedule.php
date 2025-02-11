<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'scheduleable_id',
        'date',
        'assignee_id',
        'scheduleable_type'
    ];

    public function assignee() {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function scheduleable()
    {
        return $this->morphTo();
    }

        protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $casts = [];
}
