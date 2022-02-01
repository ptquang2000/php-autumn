# php-autumn guide

## Create a Web Controller
In MVC's approach to building websites, HTTP requests are handled by a controller.
You can easily identify the controller by the `#[Controller]` annotation.
In the following example, WebController handles GET requests for:
* `/` by returning the name of a `View` (in this case, `home.html`)
* `/your-path` by returning the name of a `View` (in this case, `static.html`)
* `/your-path/$number` by returning the name of a `View` (in this case, `dynamic.php`)

A View is responsible for rendering the HTML content.
The following listing (`from app/php/WebController.php`) shows the controller:

```php
namespace App\PHP;
use Core\{Controller, RequestMapping, Model};

#[Controller]
class WebController {
    #[RequestMapping(value: '/')]
    function get_home()
    {
        return 'home.html';
    }
    #[RequestMapping(value: '/your-path')]
    function get_static_path()
    {
        return 'static.html';
    }
    #[RequestMapping(value: '/your-path/$number')]
    function get_dynamic_path($number, Model $model)
    {
        if (!is_numeric($number)) die("invalid number");
        $model->add_attribute("isEven", $number % 2 == 0);
        return 'dynamic.php';
    }
}
```
This controller is concise and simple, but there is plenty going on. We break it down step by step.

The `#[RequestMapping]` annotation ensures that HTTP GET requests to `/your-path` are mapped to the `get_static_path()` method. 
This method will render a `View` from `static.html`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <ul>
        <li><a href="/your-path/1">link to /your-path/1</a></li>
        <li><a href="/your-path/2">link to /your-path/2</a></li>
    </ul>
</body>
</html>
```

`@RequestMapping` binds the value of the query string parameter `$number` into the `$number` parameter of the `get_dynamic_path()` method.
For the view template accessing to the value of `$number` parameter, it is required to add a `Model` parameter to method, then adding everything you want to that `Model` object.

A normal PHP file is used to render the value of the `$number` and `$isEven` attributes that was set in the controller.
The following listing (from `app/templates/dynamic.php`) shows the `dynamic.php` template:
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic URL</title>
</head>
<body>
    <?php if (!$isEven):?>
        <p><?=$number?> is an even number</p>
    <?php else:?>
        <p><?=$number?> is an odd number</p>
    <?php endif?>
    <br>
    <a href="/your-path">go back</a>
</body>
</html>
</body>
</html>
```
Static resources, including HTML and JavaScript and CSS, can be served from your application by dropping them into the right place in the source code.
By default, PHP-autumn serves static content from resources in the path at `/app/static`.

As a result, you need to create the following file (which you can find in /app/static/home.html):

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <ul>
        <li><a href="/your-path">link to fixed url</a></li>
    </ul>
</body>
</html>
```

## Test Application with Docker


Run these command to start the application

```bash
docker build . -t webcontroller
docker run -p 127.24.0.4:80:80 webcontroller
```

Now the website is running, the home HTML can be found at `http://127.24.0.4/home.html`.

Visiting `http://127.24.0.4/your-path`, where you should see 2 links:
* /your-path/1
* /your-path/2

Visiting the first link `http://127.24.0.4/your-path/1`, where you should see "1 is an odd number".

Then visiting `http://127.24.0.4/your-path/2`. Notice how the message changes from “1 is an odd number” to “2 is an even number”:

The `$number` parameter can be explicitly overridden through the path string. 

![image][../assets/web-controller.gif]