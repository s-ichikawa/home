<?php

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

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

    public static function del($key)
    {
        self::getInstance()->redis->del($key);
    }
}

const GITHUB_README = 'github::readme::';

function getUrls()
{
    $keys = RedisCli::keys(GITHUB_README . '*');
    foreach ($keys as $key) {
        $readme = json_decode(RedisCli::get($key));
        if (!$url = $readme->download_url) {
            continue;
        }
        yield $url;
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

RedisCli::del('badge:*');
RedisCli::del('service:*');

$client = new Client();
$services = [];
foreach (getUrls() as $url) {
    echo ++$i . ':' . PHP_EOL;

    $promise = $client->requestAsync('GET', $url)
        ->then(function (ResponseInterface $response) use ($url, $services) {
            $badges = getBadges($url, $response->getBody()->getContents());
            foreach ($badges as $badge) {
                $badge_name = parse_url($badge['badge']);
                RedisCli::incr('badge', $badge_name['host'] . $badge_name['path']);
                echo '    badge: ' . $badge_name['host'] . $badge_name['path'] . PHP_EOL;

                $service_name = (parse_url($badge['service'])['host'] ?? $badge['service']);
                if (!in_array($service_name, $services)) {
                    RedisCli::incr('service', $service_name);
                    $services[] = $service_name;
                    echo '    service: ' . (parse_url($badge['service'])['host'] ?? $badge['service']) . PHP_EOL;
                }
            }
        })
        ->otherwise(function ($reason) {
            echo $reason . PHP_EOL;
        })
        ->wait();
}
