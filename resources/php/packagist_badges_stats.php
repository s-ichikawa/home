<?php
use GuzzleHttp\Client;

require_once __DIR__ . '/../../vendor/autoload.php';

$env = require __DIR__ . '/../../.env.php';

$id = $env['GITHUB_ID'];
$secret = $env['GITHUB_PASSWD'];

$redis = new Redis();
$redis->connect('127.0.0.1');
if (!$redis->ping()) {
    throw new Exception('Redis cant connect.');
}


$client = new Client();
if (!$json = $redis->get('list_json')) {
    $response = $client->get('https://packagist.org/packages/list.json');

    if ($response) {
        $json = $response->getBody()->getContents();
        $redis->set('list_json', $json);
    }
    echo 'request succeeded.' . PHP_EOL;
}

$data = json_decode($json);
$i = 0;
foreach ($data->packageNames as $packageName) {

    // test
    $packageName = '0100dev/cakephp-rabbitmq';

    $source_url = sprintf('https://packagist.org/packages/%s.json', $packageName);
    echo $i . ': ' . $source_url . PHP_EOL;
    $res = $client->get($source_url);
    $package = json_decode($res->getBody()->getContents())->package;
    if (strpos($package->repository, 'github.com')) {
        try {
            $contents_url = str_replace('github.com/', 'api.github.com/repos/', $package->repository) . '/contents?client_id=' . $id . '&client_secret=' . $secret;
            $res = $client
                ->get($contents_url)
                ->getBody();
            $contents = json_decode($res);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            echo $exception . PHP_EOL;
            continue;
        }

        foreach ($contents as $content) {
            if (preg_match('/readme/i', $content->name)) {
                $readme = $client->get($content->download_url)->getBody()->getContents();
                preg_match_all('/\[\!\[(.*?)\]\((.*?)\)\]\((.*?)\)/', $readme, $matches);

                $badges = $matches[2];
                $services = $matches[3];
                $count = count($badges);
                for ($j = 0; $j < $count; $j++) {
                    $badge = str_replace($package->repository, 'user/repo', $badges[$j]);
                    $service = str_replace($package->repository, 'user/repo', $services[$j]);

                    var_dump($badge, $service);
                }

                $i++;
                break;
            }
        }
        break;
    } else {
        echo 'unknown repository. => ' . $package->repository;
    }
}

