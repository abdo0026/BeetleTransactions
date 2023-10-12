<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if(!is_null(config('globals.user'))){
            $loggedIn = config('globals.user');
            $builder->where($model->getTable().'.user_id', $loggedIn->id);
        }
    }
}
