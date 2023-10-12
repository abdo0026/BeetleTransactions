<?php

namespace App\Traits;



use App\Models\Scopes\UserScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait UserRule
{

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    protected static function bootUserRule()
    {

        static::addGlobalScope(new UserScope());

        static::creating(function ($query) {
            if(!is_null(config('globals.user'))){
                $loggedInUser = config('globals.user');
                $query->user_id = $loggedInUser->id;
            }
        });
    }
}
