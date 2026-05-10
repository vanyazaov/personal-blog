<?php

declare(strict_types=1);

return [
    ['GET', '/', 'App\Controllers\HomeController@index'],
    ['GET', '/category/{id}', 'App\Controllers\PostsController@index'],
    ['GET', '/category/{id}/{article_id}', 'App\Controllers\PostsController@show'],
];
