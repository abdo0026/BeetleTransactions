<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\DomainData\UserDto;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, UserDto, SoftDeletes, HasRoles;

     protected $fillable = [];
     
     public $relationFilters = [];

    protected $hidden = [
        'password',
        'salt'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    public function registerationValidation(){
        return $this->hasOne(RegisterationValidation::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
