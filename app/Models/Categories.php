<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\DomainData\CategoriesDto;

class Categories extends Model
{
    use SoftDeletes, CategoriesDto;

    protected $fillable = [];
     
    public $relationFilters = [];

    public function subCategories(){
        return $this->hasMany(SubCategories::class, 'category_id');
    }
}
