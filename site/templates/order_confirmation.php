<?php

namespace ProcessWire;

function sendOrder($order) {
	$header = '<h2>На сайте создан новый ' . $order->title . '</h2>';
	$header .= "<hr>";
	$header .= '<h3>информация о клиенте:</h3>';
	$header .= "<p>Имя - <b>" . $order->customer->firstname . ' ' . $order->customer->lastname . "</b></p>";
	$header .= "<p>Электронная почта - <b>" . $order->customer->email . "</b></p>";
	$header .= "<p>Телефон - <b>" . $order->customer->main_phone . "</b></p>";
	$header .= "<hr>";

	$body = '<h3>Информация о заказе:</h3>';
	$body .= '<p>Дата заказа: ' . dateTime('d.m.Y H:i', $order->created) . '</p>';
	$body .= '<p>Статус заказа: ' . getOrderStatus($order) . '</p>';
	if ($order->payment) {
		$body .= '<p>Метод оплаты: ' . $order->payment->title . '</p>';
	}
	if ($order->delivery) {
		$body .= '<p>Доставка: ' . $order->delivery->title . '</p>';
	}
	if ($order->address == '') {
		$body .= '<p>Адрес: ' . wire('pages')->get('/')->address . '</p>';
	} else {
		$body .= '<p>Адрес: ' . $order->address . '</p>';
	}
	if ($order->summary) {
		$body .= '<p>Примечание: ' . $order->summary . '</p>';
	}
	$body .= '<table style="width: 100%; margin-bottom: 20px; border: 1px solid #dddddd; border-collapse: collapse;">';
	$body .= '<thead>';
	$body .= '<tr>';
	$body .= '<th style="font-weight: bold; padding: 5px; background: #efefef; border: 1px solid #dddddd;text-align: center;">Товар</th>';
	$body .= '<th style="font-weight: bold; padding: 5px; background: #efefef; border: 1px solid #dddddd;text-align: center;">Количество</th>';
	$body .= '<th style="font-weight: bold; padding: 5px; background: #efefef; border: 1px solid #dddddd;text-align: center;">Сумма</th>';
	$body .= '</tr>';
	$body .= '</thead>';
	$body .= '<tbody>';
	$total = 0;
	$tq = 0;
	foreach ($order->products as $product) {
		$body .= '<tr>';
		$body .= '<td style="border: 1px solid #dddddd;padding: 5px;">';
		$body .= '<p>' . $product->product->title . ' Размер: ' . $product->size->title . '</p>';
		$body .= '</td>';
		$body .= '<td style="border: 1px solid #dddddd;padding: 5px;text-align: center;">' . $product->quantity . '</td>';
		$total = $total + $product->price * $product->quantity;
		$tq = $tq + intval($product->quantity);
		$body .= '<td style="border: 1px solid #dddddd;padding: 5px;text-align: right;">' . formatMoney($product->price * $product->quantity)  . '</td>';
		$body .= '</tr>';
	}
	$body .= '<tr>';
	$body .= '<td style="border: 1px solid #dddddd;padding: 5px;">';
	$body .= '<p>Доставка</p>';
	$body .= '</td>';
	$body .= '<td style="border: 1px solid #dddddd;padding: 5px;text-align: center;"></td>';
	$total = $total + $order->delivery_price;
	$body .= '<td style="border: 1px solid #dddddd;padding: 5px;text-align: right;">' . formatMoney($order->delivery_price)  . '</td>';
	$body .= '</tr>';
	$body .= '</tbody>';
	$body .= '</table>';
	$body .= '<hr>';
	$body .= '<h4>Всего: (' . $tq . ' шт.)</h4>';
	$body .= '<h4>Стоимость заказа: ' . formatMoney($total) . '</h4>';
	$body .= "<hr>";
	$body .= '<p><em>' . wire('pages')->get('/')->title . '</em></p>';
	$body .= "<p><em>Это сообщение создано автоматически и не требует ответа.</em></p>";

	//отправка почты менеджерам
	$mail = wire('mail')->new();
	$mail->subject(sprintf('Новый %s', $order->title));
	$mail->to(wire('pages')->get('/')->email);
	$mail->bodyHTML('<html><body>' . $header . $body . '</body></html>');
	$mail->send();

	//отправка почты клиенту
	$header_client = '<h2>Спасибо за ваш заказ!</h2>';
	$header_client .= '<p>Номер вашего заказа - <b>' . $order->title . '</b></p>';
	$header_client .= "<p>В ближайшее время наши менеджеры свяжутся с вами для уточнения деталей.</p>";
	$header_client .= "<hr>";

	$mail_client = wire('mail')->new();
	$mail_client->subject(sprintf('Новый %s', $order->title));
	$mail_client->to($order->customer->email);
	$mail_client->bodyHTML('<html><body>' . $header_client . $body . '</body></html>');
	$mail_client->send();
}

$message = $page->error_message;

if ($input->post('neworder') == 1) {
	if ($session->CSRF->hasValidToken()) {
		$cart = $session->get('cart');
		$cart_products = [];
		foreach ($cart['products'] as $item) {
			$cart_products[] = [
				'product' => $pages->get('template=product, id=' . $item['product']),
				'size' => $pages->get('template=size, id=' . $item['size']),
				'qnt' => $item['qnt'],
				'price' => $item['price']
			];
		}

		$tq = 0;
		$total = 0;
		$delivery_type = $sanitizer->sanitize($input->post('delivery_type'), "text");
		$delivery_price = $sanitizer->sanitize($input->post('delivery_price'), "float");

		$fio = $sanitizer->sanitize($input->post('fio'), "text");
		$email = $sanitizer->sanitize($input->post('email'), "text");
		$phone = $sanitizer->sanitize($input->post('phone'), "text");
		if (!$user->isLoggedin()) {
			if (!$users->get('email=' . $email) instanceof NullPage) {
				$customer = $users->get('email=' . $email);
			} else {
				$customer = $users->newUser();
				$customer->addRole('customer');
				$pw = new Password();
				$customer->pass = $pw->randomBase64String(10);
				$customer->set('title', $fio);
				$customer->set('firstname', $fio);
				$customer->set('email', $email);
				$customer->set('main_phone', $phone);
				$customer->save();
				$users->setCurrentUser($customer);
			}
		} else {
			$customer = $user;
		}

		$order = new Page;
		$order->template = $templates->get('order');
		$order->parent = $pages->get('template=orders');
		$order->customer = $customer;
		$order->order_status = 1;
		$order->delivery = $pages->get($input->post->text('delivery'));
		$order->payment = $pages->get($input->post->text('payment'));
		$order->delivery_price = $delivery_price;
		if ($input->post->text('address')) {
			$order->address = $input->post->text('address');
		} elseif ($user->address) {
			$order->address = $user->address;
		} else {
			$order->address = $pages->get('/pickup/')->title;
		}
		if ($input->post->text('delivery_type')) {
			$order->summary = $input->post->textarea('delivery_type');
		}
		$order->save();
		$order->setAndSave('title', 'Заказ №' . $order->id);

		foreach ($cart_products as $item) {
			$order->of(false);
			$products = $order->products->getNew();
			$products->product = $item['product'];
			$products->size = $item['size'];
			$products->quantity = $item['qnt'];
			$products->price = $item['price'];
			$order->products->add($products);
			$order->save();

			//списываем с остатков
			$product = $pages->get('template=product, id=' . $item['product']);
			$current_size = $product->sizes->get('size=' . $item['size']);
			$current_quantity = $current_size->quantity;
			$new_quantity = $current_quantity - $item['qnt'];
			if ($new_quantity < 0) {
				$new_quantity = 0;
			}
			$current_size->setAndSave('quantity', $new_quantity);
		}

		$session->set('cart', ['products' => []]);
		sendOrder($order);
		$message = $page->success_message;
		$message .= '<div>Номер вашего заказа - ' . $order->title . '</div>';
	}
}

?>

<div id="content">
	<div class="uk-container uk-padding">
		<?= $message;  ?>
		<hr>
		<div class="uk-grid-medium uk-flex-between@m uk-flex-center" uk-grid>
			<div class="uk-margin">
				<a class="uk-button uk-button-default uk-button-large" href="<?= $pages->get('/catalog/')->url ?>"><span uk-icon="chevron-left"></span> В каталог</a>
			</div>
			<div class="uk-margin">
				<a class="uk-button uk-button-default uk-button-large" href="<?= $pages->get('/cabinet/')->url ?>"><span uk-icon="user"></span> Личный кабинет</a>
			</div>
		</div>
	</div>
</div>