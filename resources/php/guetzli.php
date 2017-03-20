<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../core/helper.php';

function compression()
{
    $jpeg = new SplFileInfo(resources_path('images/Beer.JPG'));
    if ($jpeg->isReadable() === false) {
        echo '1';
        exit();
    }

    $guetzli = new SplFileInfo('/usr/local/bin/guetzli');
    if ($guetzli->isExecutable() === false) {
        echo '2';
        exit();
    }

    $bench = new Ubench();

    $cmd = '/usr/local/bin/guetzli --verbose';
    $src = $jpeg->getRealPath();
    $dist = public_path('img/comp_' . $jpeg->getFilename());

    $bench->start();

    echo "$cmd $src $dist" . PHP_EOL;
    exec("$cmd $src $dist");

    $bench->end();

    echo "time: {$bench->getTime()}";
}

?>
<style>
  img {
    width: 700px;
    transform: rotate(90deg);
  }
</style>

<img src="/img/comp_Beer.JPG">
<img src="/img/Beer.JPG">
