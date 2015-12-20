<?php
namespace app;
class Registry {

    public function __construct() 
    {
    }
    
    private $data = array();
    //overload set and get to refer to data array;
    public function __set($index, $value)
    {
       $this->data[$index] = $value;
    }

   public function __get($index)
   {
       return $this->data[$index];
   }


}

