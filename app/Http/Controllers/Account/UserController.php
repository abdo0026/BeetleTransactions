<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DomainData\FilterDto;
use App\DomainData\UserDto;

class UserController extends Controller
{
    use FilterDto, UserDto;

    
}
