<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, Model};

#[Controller]
class WebController {
    #[RequestMapping(value: '/')]
    function get_home()
    {
        return 'home.html';
    }
    #[RequestMapping(value: '/url')]
    function get_static_url()
    {
        return 'static.html';
    }
    #[RequestMapping(value: '/url/$number')]
    function get_dynamic_url($number, Model $model)
    {
        if (!is_numeric($number)) die("invalid number");
        $model->add_attribute("isEven", $number % 2 == 0);
        return 'dynamic.php';
    }
}

?>