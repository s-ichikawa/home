<?php
namespace Sichikawa\Home\Core;


class Request
{
    public function get($key = null, $default = null)
    {
        return $this->getVal($_GET, $key, $default);
    }

    public function post($key = null, $default = null)
    {
        return $this->getVal($_POST, $key, $default);
    }

    public function files($key = null, $default = null)
    {
        return $this->getVal($_FILES, $key, $default);
    }

    public function cookie($key = null, $default = null)
    {
        return $this->getVal($_COOKIE, $key, $default);
    }

    public function server($key = null, $default = null)
    {
        return $this->getVal($_SERVER, $key, $default);
    }

    private function getVal($request, $key = null, $default = null)
    {
        if (empty($key)) {
            return $request;
        }
        return $request[$key] ?? $default;
    }
}