<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, Model};

#[Controller]
class WebController {
    #[RequestMapping(value: '/', method: RequestMethod::GET)]
    function get_home()
    {
        return 'home.html';
    }
    #[RequestMapping(value: '/url', method: RequestMethod::GET)]
    function get_static_url()
    {
        return 'static.html';
    }
    #[RequestMapping(value: '/url/$number', method: RequestMethod::GET)]
    function get_dynamic_url($number, Model $model)
    {
        if (is_numeric($number)) else die("invalid number");
        $model->add_attribute("isEven", $number % 2 == 0);
        return 'dynamic.php';
    }
}

?>