<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\DomainData\TransactionDto;
use App\Traits\UserRule;

class Transaction extends Model
{
    use SoftDeletes, TransactionDto, UserRule;

    protected $fillable = [];
     
    public $relationFilters = [];

    
    public function category(){
        return $this->belongsTo(Categories::class)->withTrashed();;
    }

    public function subCategory(){

        return $this->belongsTo(SubCategories::class, 'sub_category_id')->withTrashed();;
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_id')->withTrashed();
    }
    

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}
