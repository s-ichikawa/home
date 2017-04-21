<?php

add_get('index', \Sichikawa\Home\App\Controllers\IndexController::class);

add_get('controller', \Sichikawa\Home\App\Controllers\IndexController::class, 'test');
add_get('draw_search', \Sichikawa\Home\App\Controllers\IndexController::class, 'draw_search');

add_post('controller', \Sichikawa\Home\App\Controllers\IndexController::class);

add_get('test', function () {
    echo __FUNCTION__;
});
