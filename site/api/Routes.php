<?php

namespace ProcessWire;

require_once wire('config')->paths->AppApi . 'vendor/autoload.php';
require_once wire('config')->paths->AppApi . 'classes/AppApiHelper.php';

// require_once __DIR__ . '/Example.php';
require_once __DIR__ . '/Products.php';

$routes = [
	// ['OPTIONS', 'test', ['GET']], // this is needed for CORS Requests
	// ['GET', 'test', Example::class, 'test'],

	// 'users' => [
	// 	['OPTIONS', '', ['GET']], // this is needed for CORS Requests
	// 	['GET', '', Example::class, 'getAllUsers', ['auth' => true]],
	// 	['OPTIONS', '{id:\d+}', ['GET']], // this is needed for CORS Requests
	// 	['GET', '{id:\d+}', Example::class, 'getUser', ['auth' => true]], // check: https://github.com/nikic/FastRoute
	// ],

	// 'getproductid' => [
	// 	['OPTIONS', '{id:\d+}', ['GET']], // this is needed for CORS Requests
	// 	['GET', '{id:\d+}', App::class, 'getProductID', ['auth' => false]],
	// ],

	// 'getproducts' => [
	// 	['OPTIONS', '', ['GET']],
	// 	['GET', '', Products::class, 'getProducts', ['auth' => false]],
	// ],

	'getcategories' => [
		['OPTIONS', '{section}', ['GET']], // this is needed for CORS Requests
		['GET', '{section}', Products::class, 'getcategories', ['auth' => false]],
	],

	'getproducts' => [
		['OPTIONS', '{section}', ['GET']], // this is needed for CORS Requests
		['GET', '{section}/{category}/{size}/{page}', Products::class, 'getProducts', ['auth' => false]],
	],

	'getproductname' => [
		['OPTIONS', '{name}', ['GET']], // this is needed for CORS Requests
		['GET', '{name}', Products::class, 'getProductName', ['auth' => false]],
	],
];
