<?php
declare(strict_types=1);

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

			// 基礎資訊
			Router::get('/info', 'App\Controller\Api\UserController@info');

			// 關注列表
			Router::get('/subscript', 'App\Controller\Api\UserController@subscriptList');

			// 關注
			Router::post('/subscript', 'App\Controller\Api\UserController@subscript');

			// 取消關注
			Router::post('/cancel-subscript', 'App\Controller\Api\UserController@cancelSubscript');

			// 粉絲列表
			Router::get('/fans', 'App\Controller\Api\UserController@fans');
			
			// 好友列表
			Router::get('/friends', 'App\Controller\Api\UserController@friends');
		});

	}, 
	[
		'middleware' => [
			UserAuthMiddleware::class,
		],
	]);

});
