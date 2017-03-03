<?php
namespace Sichikawa\Home\Core;


use Sichikawa\Home\Core\Exceptions\NotFoundException;

class Root
{

    public function call()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        $filename = pathinfo($uri, PATHINFO_FILENAME);
        $controller_name = "Sichikawa\\Home\\App\\Controllers\\" . ucfirst(($filename ?: 'index') . 'Controller');

        if (!class_exists($controller_name)) {
            throw new NotFoundException();
        }

        $controller = new $controller_name();
        if (!method_exists($controller, $method)) {
            throw new NotFoundException();
        }

        echo $controller->$method();
    }


}