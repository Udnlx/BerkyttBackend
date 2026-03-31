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
			$result = [];
			$totalQuantity = 0; // общее кол-во единиц товара
			$totalPrice    = 0; // общая сумма
			foreach ($order->products as $item) {
				$qty   = (int) $item->quantity;
				$priceRaw = (string) $item->price;
  				$price = (float) str_replace([' ', ','], ['', '.'], $priceRaw);
				$result[] = [
					'id' => $item->product->id,
					'name' => $item->product->name,
					'product' => $item->product->title,
					'size' => $item->size->title,
					'sizeRussian' => $item->size->russian_size,
					'quantity' => $item->quantity,
					'price' => $item->price,
				];
				$totalQuantity += $qty;
  				$totalPrice    += $price * $qty;
			}
			$cart = [
				'items' => $result,
				'totalQuantity' => $totalQuantity,
				'totalPrice' => $totalPrice,
			];

			$userOrders[] = [
				'id' => $order->id,
				'name' => $order->name,
				'title' => $order->title,
				'dateOrder' => wireDate('d.m.Y, H:i', $order->created),
				'cart' => $cart,
				'address' => $order->address,
				'deliveryPrice' => $order->delivery_price,
				'totalSum' => $totalPrice + $order->delivery_price,
				'orderStatus' => $order->order_status->title,
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




	public static function replaceUserData($data) {
		if(is_object($data)) $data = (array) $data;

		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);

		$response = new \StdClass();

		$user = wire('user');

		$user->of(false);
		if(isset($data['name'])) {
			$user->firstname = trim((string)$data['name']);
		}
		if(isset($data['email'])) {
			$user->email = trim((string)$data['email']);
		}
		if(isset($data['phone'])) {
			$user->main_phone = trim((string)$data['phone']);
		}
  		$user->save(['firstname', 'email', 'main_phone']);
		$user->of(true);

		$userInfo = [
			'success' => true,
			'user' => [
				'id' => $user->id,
				'name' => $user->name,
				'firstname' => $user->firstname,
				'email' => $user->email,
				'phone' => $user->main_phone,
			]
		];

		$response->userInfo = $userInfo;

		return $response;
	}
}
