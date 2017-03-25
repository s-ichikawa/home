<?php
namespace Sichikawa\Home\App\Controllers;

use Sichikawa\Home\Core\Controller;
use Sichikawa\Home\Core\View;

class IndexController extends Controller
{

    public function get()
    {
        var_dump($this->getRequest()->get('test'));
    }

    public function post()
    {
        echo 'Yes POST';
    }

    public function test()
    {
        echo __FUNCTION__ . '!!!';
    }
}