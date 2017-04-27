<?php
require_once __DIR__ . '/../../vendor/autoload.php';


$client = new \GuzzleHttp\Client();

$response = $client->get('https://packagist.org/packages/list.json');

$json = json_decode($response->getBody()->getContents());
foreach ($json->packageNames as $key => $value) {

    $source_url = sprintf('https://packagist.org/packages/%s.json', $value);

    $res = $client->get($source_url);
    $package = json_decode($res->getBody()->getContents())->package;

    echo $package->repository;
    break;
}

