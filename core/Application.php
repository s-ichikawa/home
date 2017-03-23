<?php
namespace Sichikawa\Home\Core;


class Application
{
    public $app = [];

    public function __construct()
    {

        require_once __DIR__ . '/../config/route.php';

        $this->app['route'] = route();
    }

    public function handle()
    {
        $route = $this->get('route');
        echo $route->call();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->app[$name];
    }
}