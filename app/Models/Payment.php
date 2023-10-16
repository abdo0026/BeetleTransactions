<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\DomainData\PaymentDto;
use App\Traits\UserRule;

class Payment extends Model
{
    use SoftDeletes, PaymentDto, UserRule;

    protected $fillable = [];
     
    public $relationFilters = [];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class)->withTrashed();
    }
}
