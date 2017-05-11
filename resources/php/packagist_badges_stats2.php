<?php

namespace Packagist\Stats;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Redis;

require_once __DIR__ . '/../../vendor/autoload.php';

class RedisCli
{
    /**
     * Cache constructor.
     */
    private function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1');
        if (!$this->redis->ping()) {
            throw new Exception('Redis cant connect.');
        }
    }

    public static function getInstance()
    {
        static $instance;
        if ($instance) {
            return $instance;
        }
        return $instance = new self();
    }

    public static function keys($pattern)
    {
        return self::getInstance()->redis->keys($pattern);
    }

    public static function get($key)
    {
        return self::getInstance()->redis->get($key);
    }

    public static function set($key, $val)
    {
        return self::getInstance()->redis->set($key, $val);
    }

    public static function incr($key, $member)
    {
        self::getInstance()->redis->zIncrBy($key, 1, $member);
    }
}

const PACKAGIST_PACKAGE = 'packagist::package::';
const GITHUB_README = 'github::readme::';


/*
 * Packagistからパッケージ情報を取得
 */
$getPackageUrls = function () {
    if (!$json = RedisCli::get('list_json')) {
        if ($response = (new Client)->get('https://packagist.org/packages/list.json')) {
            $json = $response->getBody()->getContents();
            RedisCli::set('list_json', $json);
        }
    }
    foreach (json_decode($json)->packageNames as $name) {
        if (RedisCli::get(PACKAGIST_PACKAGE . $name)) {
            echo $name . ': exists' . PHP_EOL;
            continue;
        }
        $url = sprintf('https://packagist.org/packages/%s.json', $name);
        yield new Request('GET', $url);
    }
};

$client = new Client();
$pool1 = new Pool($client, $getPackageUrls(), [
    'concurrency' => 1000,
    'fulfilled'   => function (ResponseInterface $response) {
        $json = $response->getBody()->getContents();
        $name = json_decode($json)->package->name;
        RedisCli::set(PACKAGIST_PACKAGE . $name, $json);
        echo $name . ': fulfilled.' . PHP_EOL;
    },
    'reject'      => function ($reason, $index) {
        echo $reason . PHP_EOL;
    },
]);
//$pool1->promise()->wait();


/*
 *
 */
function getReadMeCacheKey($url)
{
    $data = parse_url($url);
    $path = explode('/', $data['path']);
    return GITHUB_README . $path[1] . '_' . $path[2];
}

$getRepositoryUrls = function () {
    $env = require __DIR__ . '/../../.env.php';
    $id = $env['GITHUB_ID'];
    $secret = $env['GITHUB_PASSWD'];

    $packageNames = RedisCli::keys(PACKAGIST_PACKAGE . '*');
    foreach ($packageNames as $name) {
        $package = json_decode(RedisCli::get($name))->package;
        $repository = $package->repository;
        if (!strpos($repository, 'github.com')) {
            echo 'unknown repository. => ' . $repository . PHP_EOL;
            continue;
        }

        $cache_key = getReadMeCacheKey($repository);
        if (RedisCli::get($cache_key)) {
            echo $cache_key . ': exists.' . PHP_EOL;
            continue;
        }
        echo str_replace('github.com/', 'api.github.com/repos/', $repository)
            . '/contents?client_id=' . $id . '&client_secret=' . $secret . PHP_EOL;
        yield new Request('GET', str_replace('github.com/', 'api.github.com/repos/', $repository)
            . '/contents?client_id=' . $id . '&client_secret=' . $secret);
    }
};

$pool2 = new Pool($client, $getRepositoryUrls(), [
    'concurrency' => 2,
    'fulfilled'   => function (ResponseInterface $response, $index) {
        $json = $response->getBody()->getContents();

        foreach (json_decode($json) as $file) {
            if (preg_match('/readme/i', $file->name)) {
                $cache_key = getReadMeCacheKey($file->html_url);
                RedisCli::set($cache_key, json_encode($file));
                echo $cache_key . ': fulfilled.' . PHP_EOL;
                break;
            }
        }
    },
    'rejected'      => function ($reason, $index) {
        echo $reason . PHP_EOL;
        if ($reason instanceof ClientException && $reason->getResponse()->getStatusCode() == 403) {
            sleep(60);
        }
    },
]);
$pool2->promise()->wait();

$getContentsUrls = function () {
    foreach (RedisCli::keys(GITHUB_README . '*') as $contents) {

    }
};

foreach (RedisCli::keys(GITHUB_README . '*') as $contents) {
    var_dump($contents);
}