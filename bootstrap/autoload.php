<?php

require_once __DIR__ . '/../vendor/autoload.php';

function base_path($path = null)
{
    return __DIR__ . '/../' . $path;
}

function public_path($path = null)
{
    return base_path('public/' . $path);
}