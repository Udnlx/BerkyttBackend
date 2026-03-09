<?php

namespace ProcessWire;

$login = $modules->get('LoginRegister')->execute();

if ($user->isLoggedin()) {
	$content .= '<div class="uk-grid-divider" uk-grid>';
	$content .= '<div class="uk-width-1-4@m">';
	$content .= '<div class="uk-heading-divider uk-margin-small-bottom">Здравствуйте, <span class="uk-text-secondary">' . $user->firstname . '</span></div>';
	$content .= '<ul class="uk-nav uk-nav-default uk-text-uppercase">';
	if ($input->urlSegment1 == 'orders') {
		$content .= '<li class="uk-active"><a href="/cabinet/orders/">Заказы</a></li>';
	} else {
		$content .= '<li><a href="/cabinet/orders/">Заказы</a></li>';
	}
	if ($input->profile == '1') {
		$content .= '<li class="uk-active"><a href="/cabinet/?profile=1">Личные данные</a></li>';
	} else {
		$content .= '<li><a href="/cabinet/?profile=1">Личные данные</a></li>';
	}
	$content .= '<li class="uk-nav-divider"></li>';
	$content .= '<li><a href="/cabinet/?logout=1">Выйти из кабинета</a></li>';
	$content .= '</ul>';
	$content .= '</div>';
	$content .= '<div class="uk-width-3-4@m">';
	if ($input->urlSegment(1) == 'orders') {
		if ($input->urlSegment(2)) {
			$current_order = $pages->get(substr($input->urlSegment(2), 5));
			$content .= '<h2 class="uk-heading-divider">' . $current_order->title . '</h2>';
			$content .= '<div class="uk-flex uk-flex-middle uk-flex-between">';
			$content .= '<div>Дата заказа: ' . dateTime('d.m.Y', $current_order->created) . '</div>';
			$content .= '<div>Статус заказа: ' . getOrderStatus($current_order) . '</div>';
			$content .= '</div>';
			$content .= '<dl class="uk-description-list">';
			$content .= '<dt>Адрес</dt>';
			if ($current_order->address == '') {
				$content .= '<dd>' . $homepage->address . '</dd>';
			} else {
				$content .= '<dd>' . $current_order->address . '</dd>';
			}
			if ($current_order->summary != '') {
				$content .= '<dd>' . $current_order->summary . '</dd>';
			}
			if ($current_order->delivery) {
				$content .= '<dt>Доставка</dt>';
				$content .= '<dd>' . $current_order->delivery->title . '</dd>';
			}
			if ($current_order->payment) {
				$content .= '<dt>Метод оплаты</dt>';
				$content .= '<dd>' . $current_order->payment->title . '</dd>';
			}
			$content .= '</dl>';
			$content .= '<table class="uk-table uk-table-middle uk-table-responsive uk-table-divider">';
			$content .= '<thead>';
			$content .= '<tr>';
			$content .= '<th class="uk-text-left">Товар</th>';
			$content .= '<th class="uk-text-center uk-width-xsmall">Количество</th>';
			$content .= '<th class="uk-text-right uk-width-small">Сумма</th>';
			$content .= '</tr>';
			$content .= '</thead>';
			$content .= '<tbody>';
			$total = 0;
			$tq = 0;
			foreach ($current_order->products as $product) {
				$content .= '<tr>';
				$content .= '<td class="uk-text-left">';
				$content .= '<div class="uk-flex uk-flex-middle">';
				$content .= '<div class="uk-margin-right">';
				$content .= '<img src="' . $product->product->images->first->size(100)->url . '" alt="' . $product->product->title . '">';
				$content .= '</div>';
				$content .= '<div>';
				$content .= '<div><a class="uk-link" href="' . $product->product->url . '">' . $product->product->title . '</a></div>';
				$content .= '<div class="uk-text-small uk-text-muted">Размер: ' . $sanitizer->text($product->size->body) . '</div>';
				$content .= '</div>';
				$content .= '</div>';
				$content .= '</td>';
				$content .= '<td class="uk-text-center">' . $product->quantity . '</td>';
				$total = $total + $product->price * $product->quantity;
				$tq = $tq + intval($product->quantity);
				$content .= '<td class="uk-text-right">' . formatMoney($product->price * $product->quantity)  . '</td>';
				$content .= '</tr>';
			}
			$content .= '</tbody>';
			$content .= '</table>';
			$content .= '<hr>';
			$content .= '<div class="uk-grid-small" uk-grid>';
			$content .= '<div class="uk-width-expand" uk-leader>Итого (' . $tq . ' шт.)</div>';
			$content .= '<div>' . formatMoney($total) . '</div>';
			$content .= '</div>';
			$delivery = 0;
			if ($current_order->delivery_price == '') {
				$delivery = 0;
			} else {
				$delivery = $current_order->delivery_price;
			}
			$content .= '<div class="uk-grid-small" uk-grid>';
			$content .= '<div class="uk-width-expand" uk-leader>Доставка</div>';
			$content .= '<div>' . formatMoney($delivery) . '</div>';
			$content .= '</div>';
			$content .= '<div class="uk-grid-small uk-text-bold" uk-grid>';
			$content .= '<div class="uk-width-expand" uk-leader>Стоимость заказа</div>';
			$content .= '<div>' . formatMoney($delivery + $total) . '</div>';
			$content .= '</div>';
		} else {
			$orders = $pages->find('template=order, customer=' . $user . ', sort=-created');
			$content .= '<h2 class="uk-heading-divider">Информация о заказах</h2>';
			$content .= '<table class="uk-table uk-table-justify uk-table-responsive uk-table-divider">';
			$content .= '<thead>';
			$content .= '<tr>';
			$content .= '<th class="uk-width-small">Дата</th>';
			$content .= '<th>№ Заказа</th>';
			$content .= '<th>Статус</th>';
			$content .= '<th>Сумма</th>';
			$content .= '</tr>';
			$content .= '</thead>';
			$content .= '<tbody>';
			foreach ($orders as $order) {
				$content .= '<tr>';
				$content .= '<td>' . dateTime('d.m.Y', $order->created) . '</td>';
				$content .= '<td><a href="/cabinet/orders/view-' . $order->id . '/">' . $order->title . '</a></td>';
				$content .= '<td>' . getOrderStatus($order) . '</td>';
				$total = 0;
				$tq = 0;
				foreach ($order->products as $item) {
					$total = $total + $item->price * $item->quantity;
					$tq = $tq + $item->quantity;
				}
				$content .= '<td class="uk-text-right">' . formatMoney($total)  . '</td>';
				$content .= '</tr>';
			}
			$content .= '</tbody>';
			$content .= '</table>';
		}
	} else {
		if (!$input->get('profile')) {
			$content .= $page->body;
		}
		$content .= $login;
	}
	$content .= '</div>';
	$content .= '</div>';
} else {
	$content .= '<div class="uk-flex uk-flex-center uk-width-auto">';
	$content .= '<div class="uk-width-1-2@m">';
	$content .= $login;
	$content .= '</div>';
	$content .= '</div>';
}

?>

<div id="content">
	<div class="uk-container uk-padding">
		<h1>Кабинет пользователя</h1>
		<?= $content; ?>
	</div>
</div>