<?php

namespace ProcessWire;

require_once wire('config')->paths->AppApi . 'vendor/autoload.php';
require_once wire('config')->paths->AppApi . 'classes/AppApiHelper.php';

// require_once __DIR__ . '/Example.php';
require_once __DIR__ . '/Products.php';
require_once __DIR__ . '/Mainpage.php';
require_once __DIR__ . '/Confirm.php';

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

	'maininfo' => [
		['OPTIONS', '', ['GET']],
		['GET', '', Mainpage::class, 'mainInfo', ['auth' => false]],
	],

	'topbanner' => [
		['OPTIONS', '', ['GET']],
		['GET', '', Mainpage::class, 'topBanner', ['auth' => false]],
	],

	'mainpage' => [
		['OPTIONS', '', ['GET']],
		['GET', '', Mainpage::class, 'mainPage', ['auth' => false]],
	],

	'getpage' => [
		['OPTIONS', '{page}', ['GET']],
		['GET', '{page}', Mainpage::class, 'getPage', ['auth' => false]],
	],

	'getcategories' => [
		['OPTIONS', '{section}', ['GET']],
		['GET', '{section}', Products::class, 'getCategories', ['auth' => false]],
	],

	'getproducts' => [
		['OPTIONS', '{section}', ['GET']],
		['GET', '{section}/{category}/{size}/{page}', Products::class, 'getProducts', ['auth' => false]],
	],

	'getproductname' => [
		['OPTIONS', '{name}', ['GET']],
		['GET', '{name}', Products::class, 'getProductName', ['auth' => false]],
	],

	'confirmorder' => [
		['OPTIONS', '', ['POST']],
		['POST', '', Confirm::class, 'confirmOrder', ['auth' => false]],
	],
];
