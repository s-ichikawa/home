<?php
namespace Sichikawa\Home\App\Controllers;

use Sichikawa\Home\Core\Controller;
use Sichikawa\Home\Core\View;

class RedisController extends Controller
{

    public function get()
    {
        var_dump($this->getRequest()->get('test'));
    }

    public function post()
    {
        echo 'Yes POST';
    }

    public function delete()
    {
        echo __FUNCTION__;
    }

    public function draw_search()
    {
        $config = require(base_path('.env.php'));
        (new View())->render('draw_search', [
            'key' => $config['GOOGLE_MAP_KEY']
        ]);
    }
}
