<?php
namespace Sichikawa\Home\Core;


use Sichikawa\Home\Core\Exceptions\NotFoundException;

class Route
{
    private $paths;

    private $method;

    /**
     * Route constructor.
     * @param $uri
     * @param $method
     */
    public function __construct($uri, $method)
    {
        $this->uri = $uri;
        $this->method = $method;
    }

    public function call()
    {
        $handler = $this->find();
        if ($handler instanceof \Closure) {
            return $handler();
        }

        $function = null;
        if (is_array($handler)) {
            $keys = array_keys($handler);
            $function = array_shift($keys);
            $controller_name = array_shift($handler);
        } else {
            $controller_name = $handler;
        }
        if ($this->isController($controller_name)) {
            $controller = $this->getController($controller_name);
            $controller->setRequest(new Request());
            return $controller->{$function ?:$this->method}();
        }

        $path = resources_path('php/' . $this->getFileName() . '.php');
        if ($this->isRaw($path)) {
            include_once $path;
            exit();
        }

        throw new NotFoundException();
    }

    /**
     * @param $name
     * @return Controller
     */
    private function getController($name)
    {
        return new $name();
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

    public function add($method, $path, $handler, $function = null)
    {
        if ($function) {
            $this->paths[$method][$path][$function] = $handler;
        } else {
            $this->paths[$method][$path] = $handler;
        }
    }

    private function find()
    {
        return $this->paths[strtoupper($this->method)][$this->getFileName()] ?? null;
    }
}