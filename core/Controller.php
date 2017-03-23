<?php
namespace Sichikawa\Home\Core;

class Controller
{
    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
    /**
     * @var Request
     */
    protected $request;

}