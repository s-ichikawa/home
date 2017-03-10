<?php
namespace Sichikawa\Home\Core;


use Sichikawa\Home\Core\Exceptions\NotFoundException;

class Root
{
    private $paths;

    /**
     * Root constructor.
     */
    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
    }

    public function call()
    {

        $this->getRawPhp();

        if (list($controller, $method) = $this->getController()) {
            $controller->$method();
        }

    }

    private function getController()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $filename = pathinfo($this->uri, PATHINFO_FILENAME);
        $controller_name = "Sichikawa\\Home\\App\\Controllers\\" . ucfirst(($filename ?: 'index') . 'Controller');

        if (!class_exists($controller_name)) {
            throw new NotFoundException();
        }

        $controller = new $controller_name();
        if (!method_exists($controller, $method)) {
            throw new NotFoundException();
        }

        return [$controller, $method];
    }

    private function getRawPhp()
    {
        $filename = pathinfo($this->uri, PATHINFO_FILENAME);

        $path = resources_path('php/' . $filename . '.php');

        if (file_exists($path) && is_readable($path)) {
            include $path;
            exit();
        }
    }

    public function add($method, $path, $handler)
    {
        $this->paths[] = [$method, $path, $handler];
    }

}