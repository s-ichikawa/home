<?php
namespace Sichikawa\Home\Core;


class View
{

    private $view;

    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../resources/view');
        $this->view = new \Twig_Environment($loader, [
            'cache' => storage_path('cache'),
            'debug' => true,
        ]);
    }

    public function render($path, $data = [])
    {
        echo $this->view->render($path . '.twig', $data);
    }
}