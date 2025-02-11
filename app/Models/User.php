<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function assignedSchedules() {
        return $this->hasMany(Schedule::class, 'assignee_id');
    }

    public function spaces(){
        return $this->belongsToMany(Space::class);
    }

    public function notifications(){
        return $this->hasMany(Notification::class);
    }

    public function schedules()
    {
        return $this->morphMany(Schedule::class, 'scheduleable');
    }

    public function schedulesToday()
    {
        return $this->morphMany(Schedule::class, 'scheduleable')
            ->whereDate('date', Carbon::today());
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'line_id',
        'email',
        'password',
        'photo_profile',
        'google_token',
        'google_access_token',
        'last_login',
        'is_sync_google',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->id = (string) Str::uuid();
        });
    }
}
