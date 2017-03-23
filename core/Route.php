<?php
namespace Sichikawa\Home\Core;


use Sichikawa\Home\Core\Exceptions\NotFoundException;

class Route
{
    private $paths;

    private $method;

    /**
     * Root constructor.
     */
    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function call()
    {
        $handler = $this->find();
        if ($handler instanceof \Closure) {
            return $handler();
        }

        $controller_name = $handler;
        if ($this->isController($controller_name)) {
            return (new $handler())->{$this->method}();
        }

        $path = resources_path('php/' . $this->getFileName() . '.php');
        if ($this->isRaw($path)) {
            include_once $path;
            exit();
        }

        throw new NotFoundException();
    }

    private function isController($name)
    {
        if (!isset($this->paths[$this->method][$this->getFileName()])) {
            return null;
        }
        return class_exists($name) && method_exists(new $name, $this->method);
    }

    private function isRaw($path)
    {
        return file_exists($path) && is_readable($path);
    }

    private function getFileName()
    {
        $filename = pathinfo($this->uri, PATHINFO_FILENAME);
        return preg_replace('/\?.*/', '', $filename) ?: 'index';
    }

    public function add($method, $path, $handler)
    {
        $this->paths[$method][$path] = $handler;
    }

    private function find()
    {
        return $this->paths[strtoupper($this->method)][$this->getFileName()] ?? null;
    }
}