<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
// use Laravel\Passport\HasApiTokens;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'mobile_num',
        'address',
        'has_moodle',
        'provider',
        'provider_id',
        'token',
        'has_socialite'
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
    ];

    // public function sendPasswordResetNotification($token)
    // {
    //     $url = env('REACT_APP_BASE_URL') . '/reset-password?token=' . $token;
    //     $this->notify(new ResetPasswordNotification($url));
    // }

    public function user_events(){
        return $this->hasMany(UserEvent::class,'user_id','id');
    }
}
