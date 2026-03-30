<?php

namespace ProcessWire;

require_once wire('config')->paths->AppApi . 'vendor/autoload.php';
require_once wire('config')->paths->AppApi . 'classes/AppApiHelper.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class ApiAuth {

	/**
	 * Обработка входа пользователя по email и password
	 * Возвращает JWT-токен и данные пользователя
	 */
	public static function login($data) {
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, [
			'email|string',
			'password|string'
		]);

		$email = $data->email;
		$password = $data->password;

		// Поиск пользователя по email
		$user = wire('users')->get('email=' . $email);

		// Проверка существования пользователя и его активности
		if (!$user->id || !$user->hasRole('guest')) {
			throw new \Exception('Неверный email или пароль', 401);
		}

		// Попытка входа
		try {
			$loggedIn = wire('session')->login($user->name, $password);

			// // Отладка: логируем результат входа
			// wire('log')->save('appapi-login', [
			// 	'login_attempt' => true,
			// 	'login_success' => $loggedIn,
			// 	'user_current' => wire('user')->isLoggedIn() ? wire('user')->name : 'guest'
			// ]);

			if (!$loggedIn) {
				throw new \Exception('Неверный email или пароль', 401);
			}

			// Получаем текущее приложение для генерации JWT
			$authInstance = \ProcessWire\Auth::getInstance();
			$application = $authInstance->getApplication();

			// Генерируем JWT-токен только если используется SingleJWT
			if ($application->getAuthtype() === \ProcessWire\Application::authtypeSingleJWT) {
				$token = $authInstance->___createSingleJWTToken();
			} else {
				// Для других типов аутентификации
				$token = null;
			}

			// Формируем ответ
			$response = [
				'success' => true,
				'token' => $token,
				'user' => [
					'id' => $user->id,
					'name' => $user->name,
					'title' => $user->firstname,
					'email' => $user->email,
					'phone' => $user->main_phone
				]
			];

			// Выходим из сессии (токен будет использоваться для аутентификации)
			wire('session')->logout($user);

			return $response;

		} catch (\Exception $e) {
			throw new \Exception('Неверный email или пароль', 401);
		}
	}

	/**
	 * Получение данных текущего пользователя
	 */
	public static function me($data) {
		$user = wire('user');

		if (!$user->isLoggedIn()) {
			throw new \Exception('Пользователь не авторизован', 401);
		}

		return [
			'success' => true,
			'user' => [
				'id' => $user->id,
				'name' => $user->title ?: $user->name,
				'email' => $user->email
			]
		];
	}
}
