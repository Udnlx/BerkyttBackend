<?php

namespace ProcessWire;

class Profile {
	public static function getOrders($data) {
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);

		$response = new \StdClass();

		$user = wire('user');

		$getUserOrders = wire('pages')->find('template=order, customer=' . $user->id);
		$userOrders = [];
		foreach ($getUserOrders as $order) {
			$userOrders[] = [
				'id'  => $order->id,
				'name'  => $order->name,
				'title'  => $order->title,
			];
		}

		$userInfo = [
			'success' => true,
			'user' => [
				'id' => $user->id,
				'name' => $user->title ?: $user->name,
				'email' => $user->email
			]
		];

		$response->userInfo = $userInfo;
		$response->userOrders = $userOrders;

		return $response;
	}
}
