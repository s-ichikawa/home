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


function add_root($method, $path, $handler) {
    static $root;
    if (empty($root)) {
        $root = new \Sichikawa\Home\Core\Root();
    }
    $root->add($method, $path, $handler);
};

function add_get($path, $handler) {
    add_root('get', $path, $handler);
}