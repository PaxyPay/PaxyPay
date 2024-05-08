<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'image',
        'password',
        'company_address',
        'company_name',
        'company_phone',
        'company_email',
        'vat_number'
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

    public function payments(){
        return $this->hasMany(Payment::class);
    }
    public function perPage(User $user){
        $oggetto = json_decode($user->settings);
        $perPage = $oggetto->perPage;
        $orderByName = $oggetto->orderByName;
        $orderByDate = $oggetto->orderByDate;
        $orderByDueDatet = $oggetto->orderByDueDate;
        $orderByActive = $oggetto->orderByActive;
        $orderByTotalPrice = $oggetto->orderByPrice;
      
        return ;
    }
}
