<?php

namespace App\Services;

interface IBusinessService{
    public function isValid(array $request ,\stdClass &$output) :bool;
    public function perform(array $request ,\stdClass &$output) :void;
}