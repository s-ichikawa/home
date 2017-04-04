<?php
namespace Sichikawa\Home\Core;


class Application
{
    protected $app = [];

    public function __construct()
    {
        require_once __DIR__ . '/../config/route.php';

        $this->app['route'] = route();
    }

    public function handle()
    {
        echo $this->getApp('route')->call();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getApp($name)
    {
        return $this->app[$name];
    }
}