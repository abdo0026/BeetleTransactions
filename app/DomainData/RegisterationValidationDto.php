<?php
namespace App\DomainData;

trait RegisterationValidationDto{

   public function getRules(array $fields = []) :array
    {
      $data = [
        'verification_code' => 'required|string', 
        'is_verified_email' => 'boolean',
        'expire_date' => 'date',
        'account_verified_at' => 'date',
        'user_id' => 'required|numeric',
      ];

        if(sizeof($fields) == 0)
        return $data;
       return array_intersect_key($data, array_flip($fields));
    }

    
    public function initializeRegisterationValidationDto() :void
    {
        $this->fillable = array_keys($this->getRules());
    }
}