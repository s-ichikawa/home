<?php
namespace Sichikawa\Home\Core;


class Application
{
    public $app = [];

    public function __construct()
    {
        $this->app['root'] = new Root();
    }

    public function handle()
    {
        $root = $this->get('root');
        $root->call();
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