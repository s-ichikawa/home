<?php

add_get('index', function() {
    return 'Test';
});

add_get('controller', \Sichikawa\Home\App\Controllers\IndexController::class);

add_post('controller', \Sichikawa\Home\App\Controllers\IndexController::class);
