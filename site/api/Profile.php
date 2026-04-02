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
		$dataemail = $data['email'];

		$error = '';
		$success = true;

		if($user->email == $dataemail) {
			$error = '';
			$success = true;
		} else {
			if(wire('users')->get("email=$dataemail")->id) {
				$error = 'Пользователь с таким email уже существует';
				$success = false;
			}
		}

		if ($success == true) {
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
		} else {
			$success = false;
		}

		$userInfo = [
			'success' => $success,
			'error' => $error,
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




	public static function register($data) {
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);

		$response = new \StdClass();

		$error = '';
		$success = true;

		if(wire('users')->get("email=$data->email")->id) {
			$error = 'Пользователь с таким email уже существует';
			$success = false;
		}

		$baseName = wire('sanitizer')->pageName(strstr($data->email, '@', true) ?: $data->email, true);
		if(!$baseName) $baseName = 'user';
		$name = $baseName;
		$i = 1;
		while(wire('users')->get("name=$name")->id) {
		$name = $baseName . '-' . (++$i);
		}

		if ($success == true) {
			$u = new User();
    		$u->of(false);
			$u->name = $name;
			$u->email = $data->email;
    		$u->firstname = $data->title;
			$u->main_phone = $data->phone;
			$u->pass = $data->password;
			$u->addRole('customer');
			$u->save();
    		$u->of(true);
		} else {
			$success = false;
		}

		$registerInfo = [
			'success' => $success,
			'error' => $error,
			'user' => [
				'firstname' => $data->title,
				'email' => $data->email,
				'phone' => $data->phone,
				'password' => $data->password,
			]
		];

		$response->userInfo = $registerInfo;

		return $response;
	}





	public static function addComment($data) {
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);

		$response = new \StdClass();

		$pageProduct = wire('pages')->get('template=product, name=' . $data->name);

		if ($pageProduct->id) {
			// Добавляем коммент
			$date_create = date('Y-m-d');
			$comment = new Comment;
			$comment->text = $data->feedback;
			$comment->cite = $data->author;
			$comment->created = strtotime($date_create);
			$comment->email = $data->email;
			$comment->website = 'https://berkytt.ru/';

			$pageProduct->of(false);
			$pageProduct->comments->add($comment);
			$pageProduct->save('comments');
			//Добавляем коммент
		}

		$response->idProduct = $pageProduct->id;
		$response->nameProduct = $pageProduct->name;

		return $response;
	}
}
