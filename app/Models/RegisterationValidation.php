<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\DomainData\RegisterationValidationDto;

class RegisterationValidation extends Model
{
    use SoftDeletes, RegisterationValidationDto;

    protected $fillable = [];

    public $relationFilters = [];

    public function user(){
        return $this->belongsTo(User::class)->withTrashed();
    }
}
