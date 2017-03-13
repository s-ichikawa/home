<?php
namespace Sichikawa\Home\Core;

class Controller
{
    protected $request;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
    }
}