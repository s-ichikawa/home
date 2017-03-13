<?php
namespace Sichikawa\Home\Core;


class Request
{
    public function get($key = null, $default = null)
    {
        if (empty($key)) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    public function post($key = null, $default = null)
    {
        if (empty($key)) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    public function files($key = null, $default = null)
    {
        if (empty($key)) {
            return $_FILES;
        }
        return $_FILES[$key] ?? $default;
    }

    public function cookie($key = null, $default = null)
    {
        if (empty($key)) {
            return $_COOKIE;
        }
        return $_COOKIE[$key] ?? $default;
    }

    public function server($key = null, $default = null)
    {
        if (empty($key)) {
            return $_SERVER;
        }
        return $_SERVER[$key] ?? $default;
    }
}