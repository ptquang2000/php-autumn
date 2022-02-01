<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, Model};

#[Controller]
class WebController {
    #[RequestMapping(value: '/your-path')]
    function get_static_path()
    {
        return 'static.html';
    }
    #[RequestMapping(value: '/your-path/$number')]
    function get_dynamic_url($number, Model $model)
    {
        if (!is_numeric($number)) die("invalid number");
        $model->add_attribute("number", $number);
        $model->add_attribute("isEven", $number % 2 == 0);
        return 'dynamic.php';
    }
}

?>