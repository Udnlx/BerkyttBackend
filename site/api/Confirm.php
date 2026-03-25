<?php

namespace ProcessWire;

class Confirm {
	public static function confirmOrder($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);

		$response = new \StdClass();

        // //Функция отправки писем
        // function sendOrder($order) {
        //     $header = '<h2>На сайте создан новый ' . $order->title . '</h2>';
        //     $header .= "<hr>";
        //     $header .= '<h3>информация о клиенте:</h3>';
        //     $header .= "<p>Имя - <b>" . $order->customer->firstname . ' ' . $order->customer->lastname . "</b></p>";
        //     $header .= "<p>Электронная почта - <b>" . $order->customer->email . "</b></p>";
        //     $header .= "<p>Телефон - <b>" . $order->customer->main_phone . "</b></p>";
        //     $header .= "<hr>";

        //     $body = '<h3>Информация о заказе:</h3>';
        //     $body .= '<p>Дата заказа: ' . dateTime('d.m.Y H:i', $order->created) . '</p>';
        //     $body .= '<p>Статус заказа: ' . getOrderStatus($order) . '</p>';
        //     if ($order->payment) {
        //         $body .= '<p>Метод оплаты: ' . $order->payment->title . '</p>';
        //     }
        //     if ($order->delivery) {
        //         $body .= '<p>Доставка: ' . $order->delivery->title . '</p>';
        //     }
        //     if ($order->address == '') {
        //         $body .= '<p>Адрес: ' . wire('pages')->get('/')->address . '</p>';
        //     } else {
        //         $body .= '<p>Адрес: ' . $order->address . '</p>';
        //     }
        //     if ($order->summary) {
        //         $body .= '<p>Примечание: ' . $order->summary . '</p>';
        //     }
        //     $body .= '<table style="width: 100%; margin-bottom: 20px; border: 1px solid #dddddd; border-collapse: collapse;">';
        //     $body .= '<thead>';
        //     $body .= '<tr>';
        //     $body .= '<th style="font-weight: bold; padding: 5px; background: #efefef; border: 1px solid #dddddd;text-align: center;">Товар</th>';
        //     $body .= '<th style="font-weight: bold; padding: 5px; background: #efefef; border: 1px solid #dddddd;text-align: center;">Количество</th>';
        //     $body .= '<th style="font-weight: bold; padding: 5px; background: #efefef; border: 1px solid #dddddd;text-align: center;">Сумма</th>';
        //     $body .= '</tr>';
        //     $body .= '</thead>';
        //     $body .= '<tbody>';
        //     $total = 0;
        //     $tq = 0;
        //     foreach ($order->products as $product) {
        //         $body .= '<tr>';
        //         $body .= '<td style="border: 1px solid #dddddd;padding: 5px;">';
        //         $body .= '<p>' . $product->product->title . ' Размер: ' . $product->size->title . '</p>';
        //         $body .= '</td>';
        //         $body .= '<td style="border: 1px solid #dddddd;padding: 5px;text-align: center;">' . $product->quantity . '</td>';
        //         $total = $total + $product->price * $product->quantity;
        //         $tq = $tq + intval($product->quantity);
        //         $body .= '<td style="border: 1px solid #dddddd;padding: 5px;text-align: right;">' . formatMoney($product->price * $product->quantity)  . '</td>';
        //         $body .= '</tr>';
        //     }
        //     $body .= '<tr>';
        //     $body .= '<td style="border: 1px solid #dddddd;padding: 5px;">';
        //     $body .= '<p>Доставка</p>';
        //     $body .= '</td>';
        //     $body .= '<td style="border: 1px solid #dddddd;padding: 5px;text-align: center;"></td>';
        //     $total = $total + $order->delivery_price;
        //     $body .= '<td style="border: 1px solid #dddddd;padding: 5px;text-align: right;">' . formatMoney($order->delivery_price)  . '</td>';
        //     $body .= '</tr>';
        //     $body .= '</tbody>';
        //     $body .= '</table>';
        //     $body .= '<hr>';
        //     $body .= '<h4>Всего: (' . $tq . ' шт.)</h4>';
        //     $body .= '<h4>Стоимость заказа: ' . formatMoney($total) . '</h4>';
        //     $body .= "<hr>";
        //     $body .= '<p><em>' . wire('pages')->get('/')->title . '</em></p>';
        //     $body .= "<p><em>Это сообщение создано автоматически и не требует ответа.</em></p>";

        //     //отправка почты менеджерам
        //     $mail = wire('mail')->new();
        //     $mail->subject(sprintf('Новый %s', $order->title));
        //     $mail->to(wire('pages')->get('/')->email);
        //     $mail->bodyHTML('<html><body>' . $header . $body . '</body></html>');
        //     $mail->send();

        //     //отправка почты клиенту
        //     $header_client = '<h2>Спасибо за ваш заказ!</h2>';
        //     $header_client .= '<p>Номер вашего заказа - <b>' . $order->title . '</b></p>';
        //     $header_client .= "<p>В ближайшее время наши менеджеры свяжутся с вами для уточнения деталей.</p>";
        //     $header_client .= "<hr>";

        //     $mail_client = wire('mail')->new();
        //     $mail_client->subject(sprintf('Новый %s', $order->title));
        //     $mail_client->to($order->customer->email);
        //     $mail_client->bodyHTML('<html><body>' . $header_client . $body . '</body></html>');
        //     $mail_client->send();
        // }
        // //Функция отправки писем

        $cart = $data->products;
		$cartProducts = [];
		foreach ($cart as $item) {
            $productPage = wire('pages')->get('template=product, id=' . $item->id);
            $sizePage = wire('pages')->get($item->idSize);
			$cartProducts[] = [
                'product' => $productPage->id,
                'size' => $sizePage->id,
                'qnt' => $item->qnt,
                'price' => $item->price
			];
		}

        $tq = 0;
		$total = 0;

		$fio = $data->buyer->name;
		$email = $data->buyer->email;
		$phone = $data->buyer->phone;

        // if (!$user->isLoggedin()) {
		// 	if (!$users->get('email=' . $email) instanceof NullPage) {
		// 		$customer = $users->get('email=' . $email);
		// 	} else {
		// 		$customer = $users->newUser();
		// 		$customer->addRole('customer');
		// 		$pw = new Password();
		// 		$customer->pass = $pw->randomBase64String(10);
		// 		$customer->set('title', $fio);
		// 		$customer->set('firstname', $fio);
		// 		$customer->set('email', $email);
		// 		$customer->set('main_phone', $phone);
		// 		$customer->save();
		// 		$users->setCurrentUser($customer);
		// 	}
		// } else {
		// 	$customer = $user;
		// }

        $delivery = null;
        if ($data->deliveryMethod == 'pickup') {
            $delivery = 6116;
        }
        if ($data->deliveryMethod == 'cdek') {
            $delivery = 6118;
        }

        $payment = null;
        if ($data->paymentMethod == 'card') {
            $payment = 6114;
        }
        if ($data->paymentMethod == 'cash') {
            $payment = 6115;
        }

        $order = new Page;
		$order->template = wire('templates')->get('order');
		$order->parent = wire('pages')->get('template=orders');
		$order->customer = 16329;
		$order->order_status = 1;
		$order->delivery = $delivery;
		$order->payment = $payment;
		$order->delivery_price = $data->deliveryCost;
        if ($data->deliveryMethod == 'pickup') {
            $order->address = 'Забрать со склада: г.Москва, Сормовский проезд д. 11/7';
        }
        if ($data->deliveryMethod == 'cdek') {
            $order->address = 'Доставка с помощью службы СДЭК: Адрес ПВЗ';
        }
		$order->save();
		$order->setAndSave('title', 'Заказ №' . $data->orderNumber);

        foreach ($cartProducts as $item) {
            $product = wire('pages')->get('template=product, id=' . $item['product']);
			$current_size = wire('pages')->get($item['size']);
            $product_size = $current_size->size;

            //Добавляем в корзину заказа
			$order->of(false);
			$products = $order->products->getNew();
			$products->product = $item['product'];
			$products->size = $product_size;
			$products->quantity = $item['qnt'];
			$products->price = $item['price'];
			$order->products->add($products);
			$order->save();

			//Списываем с остатков
			$current_quantity = $current_size->quantity;
			$new_quantity = $current_quantity - $item['qnt'];
			if ($new_quantity < 0) {
				$new_quantity = 0;
			}
			$current_size->setAndSave('quantity', $new_quantity);
		}

        $response->cartProducts = $cartProducts;

		return $response;
    }
}