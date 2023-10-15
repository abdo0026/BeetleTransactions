<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\DomainData\SubCategoriesDto;

class SubCategories extends Model
{
    use SoftDeletes, SubCategoriesDto;

    protected $fillable = [];
     
    public $relationFilters = [];

    public function category(){
        return $this->belongsTo(Categories::class)->withTrashed();;
    }

    public function transactions(){
        return $this->hasMany(Transaction::class, 'sub_category_id');
    }


}
