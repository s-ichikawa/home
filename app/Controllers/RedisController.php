<?php
namespace Sichikawa\Home\App\Controllers;

use Sichikawa\Home\Core\Controller;
use Sichikawa\Home\Core\View;

class RedisController extends Controller
{

    public function get()
    {
        $redis = new \Redis();

    }

}
