<?php

namespace Core;

class AntMatchers{
    public $property;
    public $http;

    public function __construct($http)
    {
      $this->http = $http;   
    }

    public function hasRole($role)
    {
        if (count($this->property) != 0)
            $this->property[array_key_last($this->property)]["role"][] = $role;
        return $this;
    }

    public function antMatchers($method, $path)
    {
        $this->property[] = [
            "path" => $path,
            "method" => $method,
            "role" => []
        ];
        return $this;
    }

    public function permitAll()
    {
        if (count($this->property) != 0)
            $this->property[array_key_last($this->property)]["role"][] = array();
        return $this;
    }

    public function and()
    {
        return $this->http;
    }
}

class HttpSecurity {
    public AntMatchers $antMatchers; 

    public function httpSecurity(){
        $http = new HttpSecurity();
        return $http;
    }

    public function authorizeRequest(){
       $this->antMatchers = new AntMatchers($this);
       return $this->antMatchers;
    }

}

?>