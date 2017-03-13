<?php
namespace Sichikawa\Home\Core;


class Application
{
    public $app = [];

    public function __construct()
    {

        require_once __DIR__ . '/../config/root.php';

        $this->app['root'] = root();
    }

    public function handle()
    {
        $root = $this->get('root');
        echo $root->call();
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