<?php

namespace Packagist\Stats;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use function GuzzleHttp\Psr7\str;
use Redis;

require_once __DIR__ . '/../../vendor/autoload.php';


class Cache
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

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function set($key, $val)
    {
        return $this->redis->set($key, $val);
    }

    public function incr($key, $member)
    {
        $this->redis->zIncrBy($key, 1, $member);
    }
}

class Packagist
{

    /**
     * Packagist constructor.
     */
    public function __construct()
    {
        $this->cache = Cache::getInstance();
        $this->client = new Client();
    }

    public function getPackageNames()
    {
        if (!$json = $this->cache->get('list_json')) {
            $response = $this->client->get('https://packagist.org/packages/list.json');

            if ($response) {
                $json = $response->getBody()->getContents();
                $this->cache->set('list_json', $json);
            }
            echo 'request succeeded.' . PHP_EOL;
        }

        return json_decode($json)->packageNames;
    }

    public function getPackage($name)
    {
        $cache_key = 'packagist::package::' . $name;
        if (!$json = $this->cache->get($cache_key)) {
            $source_url = sprintf('https://packagist.org/packages/%s.json', $name);
            $json = $this->client->get($source_url)->getBody()->getContents();
            $this->cache->set($cache_key, $json);
        }
        return json_decode($json)->package ?? null;
    }
}

class Github
{

    /**
     * Packagist constructor.
     */
    public function __construct()
    {
        $this->cache = Cache::getInstance();
        $this->client = new Client();


        $env = require __DIR__ . '/../../.env.php';
        $this->id = $env['GITHUB_ID'];
        $this->secret = $env['GITHUB_PASSWD'];
    }

    public function getContents($package)
    {
        try {
            $cache_key = 'github::contents::' . $package->name;
            if (!$json = $this->cache->get($cache_key)) {
                $url = str_replace('github.com/', 'api.github.com/repos/', $package->repository)
                    . '/contents?client_id=' . $this->id . '&client_secret=' . $this->secret;

                $json = $this->client
                    ->get($url)
                    ->getBody()->getContents();

                $this->cache->set($cache_key, $json);
            }

            return json_decode($json);
        } catch (RequestException $exception) {
            echo str($exception->getRequest());
            if ($exception->hasResponse()) {
                echo str($exception->getResponse());
            }
        }
        return null;
    }

    public function download($url)
    {
        try {
            return $this->client->get($url)->getBody()->getContents();
        } catch (RequestException $exception) {
            echo str($exception->getRequest());
            if ($exception->hasResponse()) {
                echo str($exception->getResponse());
            }
        }
        return null;
    }
}

function getBadges($repository, $readme)
{
    preg_match_all('/\[\!\[(.*?)\]\((.*?)\)\]\((.*?)\)/', $readme, $matches);

    $badges = $matches[2];
    $services = $matches[3];
    $count = count($badges);
    for ($j = 0; $j < $count; $j++) {
        $path = parse_url($repository)['path'];


        $badge = str_replace($path, '/user/repo', $badges[$j]);
        $service = str_replace($path, '/user/repo', $services[$j]);

        yield [
            'badge'   => $badge,
            'service' => $service,
        ];
    }
}

$packagist = new Packagist();
$github = new Github();
$redis = Cache::getInstance();
$i = 0;
foreach ($packagist->getPackageNames() as $packageName) {
    echo ++$i . ':' . $packageName . PHP_EOL;
    // test
//    $packageName = '0100dev/cakephp-rabbitmq';

    if (!$package = $packagist->getPackage($packageName)) {
        echo 'no package.';
        continue;
    }
    if (strpos($package->repository, 'github.com')) {

        if (!$contents = $github->getContents($package)) {
            echo 'no contents.' . PHP_EOL;
            continue;
        }

        foreach ($contents as $content) {
            if (preg_match('/readme/i', $content->name)) {
                if (!$readme = $github->download($content->download_url)) {
                    break;
                }
                $services = [];
                foreach (getBadges($package->repository, $readme) as $badge) {
                    $badge_name = parse_url($badge['badge']);
                    $redis->incr('badge', $badge_name['host'] . $badge_name['path']);
                    echo '    badge: ' . $badge_name['host'] . $badge_name['path'] . PHP_EOL;

                    $service_name = (parse_url($badge['service'])['host'] ?? $badge['service']);
                    if (!in_array($service_name, $services)) {
                        $redis->incr('service', $service_name);
                        $services[] = $service_name;
                        echo '    service: ' . (parse_url($badge['service'])['host'] ?? $badge['service']) . PHP_EOL;
                    }
                }
                break;
            }
        }
    } else {
        echo 'unknown repository. => ' . $package->repository;
    }
}

