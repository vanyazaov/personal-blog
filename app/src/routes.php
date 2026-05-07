<?php

declare(strict_types=1);

return [
    ['GET', '/', 'App\Controllers\HomeController@index'],
    ['GET', '/category/{id}', 'App\Controllers\ArticlesController@index'],
    ['GET', '/category/{id}/{article_id}', 'App\Controllers\ArticlesController@show'],
];
