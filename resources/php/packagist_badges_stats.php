<?php
use GuzzleHttp\Client;

require_once __DIR__ . '/../../vendor/autoload.php';

$cache_path = __DIR__ . '/list.json';

if (file_exists($cache_path) === false) {
    touch($cache_path);
}

$client = new Client();
$json = file_get_contents($cache_path);
if (empty($json)) {
    $response = $client->get('https://packagist.org/packages/list.json');

    if ($response) {
        $json = $response->getBody()->getContents();
        file_put_contents($cache_path, $json);
    }
    echo 'request succeeded.' . PHP_EOL;
}

$data = json_decode($json);
$i = 0;
foreach ($data->packageNames as $packageName) {

    $source_url = sprintf('https://packagist.org/packages/%s.json', $packageName);
    echo $source_url . PHP_EOL;
    $res = $client->get($source_url);
    $package = json_decode($res->getBody()->getContents())->package;
    if (strpos($package->repository, 'github.com')) {
        try {
            $contents_url = str_replace('github.com/', 'api.github.com/repos/', $package->repository) . '/contents';
            $res = $client
                ->get($contents_url)
                ->getBody();
            $contents = json_decode($res);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            continue;
        }

        foreach($contents as $content) {
            if (preg_match('/readme/i', $content->name)) {
                $readme = $client->get($content->download_url)->getBody()->getContents();
                preg_match_all('/\[\!\[(.*)\]\((.*)\)\]\((.*)\)/', $readme, $matches);
                var_dump($matches);
                $i++;
                break;
            }
        }
    } else {
        echo 'unknown repository. => ' . $package->repository;
    }
    if ($i > 20) {
        break;
    }
}

