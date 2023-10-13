<?php

namespace App\Services;

interface ICrudService{
    public function create(array $request ,\stdClass &$output) :void;

    public function update(array $request ,\stdClass &$output) :void;

    public function delete(array $request ,\stdClass &$output) :void;

    public function getByFilter(array $request ,\stdClass &$output) :void;

    public function getById(array $request ,\stdClass &$output) :void;

    public function isValidCreate(array $request, \stdClass &$output): bool;

    public function isValidUpdate(array $request, \stdClass &$output): bool;
    
}