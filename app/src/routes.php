<?php

declare(strict_types=1);

return [
    ['GET', '/', 'App\Controllers\HomeController@index'],
    ['GET', '/category/{id}', 'App\Controllers\CategoryController@index'],
    ['GET', '/category/{id}/{article_id}', 'App\Controllers\ArticleController@show'],
];
