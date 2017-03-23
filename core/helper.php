<?php


if (!function_exists('base_path')) {
    function base_path($path = null)
    {
        return __DIR__ . '/../' . $path;
    }
}

if (!function_exists('public_path')) {
    function public_path($path = null)
    {
        return base_path('public/' . $path);
    }
}

if (!function_exists('storage_path')) {
    function storage_path($path = null)
    {
        return base_path('storage/' . $path);
    }
}


if (!function_exists('resources_path')) {
    function resources_path($path = null)
    {
        return base_path('resources/' . $path);
    }
}

function route()
{
    static $route;
    if ($route) {
        return $route;
    }
    $uri = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    return $route = new \Sichikawa\Home\Core\Route($uri, $method);

}

function add_route($method, $path, $handler) {
    route()->add($method, $path, $handler);
};

function add_get($path, $handler) {
    add_route('GET', $path, $handler);
}

function add_post($path, $handler)
{
    add_route('POST', $path, $handler);
}