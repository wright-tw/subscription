<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;
use App\Middleware\Api\UserAuthMiddleware;

Router::get('/favicon.ico', function () {
    return '';
});

// 前台API
Router::addGroup('/api',function () {

	// 無須登入
	Router::addGroup('/user', function () {
		Router::post('/register', 'App\Controller\Api\UserController@register');
		Router::post('/login', 'App\Controller\Api\UserController@login');
	});

	// 需登入
	Router::addGroup('', function () {

		// 用戶
		Router::addGroup('/user', function () {
			Router::get('/info', 'App\Controller\Api\UserController@info');
			Router::post('/logout', 'App\Controller\Api\UserController@logout');
		});

	}, 
	[
		'middleware' => [
			UserAuthMiddleware::class, 
		],
	]);

});
