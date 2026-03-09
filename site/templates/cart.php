<?php

namespace ProcessWire;

if (!$session->get('cart')) {
	$session->set('cart', ['products' => []]);
}

if ($input->post('addtocart')) {
	$product = $pages->get('template=product, id=' . $input->post('addtocart', 'text'));
	$size = $pages->get('template=size, id=' . $input->post('size', 'text'));
	$qnt = $input->post('qnt', 'int');
	$price = getDiscountPrice($product);
	if ($session->get('cart')) {
		$products = $session->get('cart')['products'];
		$found = false;
		foreach ($products as $key => $value) {
			if ($products[$key]['product'] == $product->id && $products[$key]['size'] == $size->id) {
				$products[$key]['qnt'] = $products[$key]['qnt'] + $qnt;
				$found = true;
			}
		}
		if (!$found) {
			$products[] = [
				'product' => $product->id,
				'size' => $size->id,
				'qnt' => $qnt,
				'price' => $price['total']
			];
		}
		$session->set('cart', ['products' => $products]);
	}
}

if ($input->get('remove', 'text')) {
	$cart = $session->get('cart');
	foreach ($cart['products'] as $key => $value) {
		if ($input->get('remove', 'text') == $value['product']) {
			unset($cart['products'][$key]);
		}
	}
	$session->set('cart', ['products' => $cart['products']]);
}

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

$incart = '';
$tq = 0;
$total = 0;
$delivery = 0;
if (count($cart_products) > 0) {
	$incart .= '<table class="uk-table uk-table-middle uk-table-responsive uk-table-divider">';
	$incart .= '<thead>';
	$incart .= '<tr>';
	$incart .= '<th>Товар</th>';
	$incart .= '<th>Цена</th>';
	$incart .= '<th>Количество</th>';
	$incart .= '<th>Стоимость</th>';
	$incart .= '<th></th>';
	$incart .= '</tr>';
	$incart .= '</thead>';
	$incart .= '<tbody>';
	foreach ($cart_products as $item) {
		$incart .= '<tr>';
		$incart .= '<td>';
		$incart .= '<a class="uk-flex uk-flex-middle" href="' . $item['product']->url . '">';
		$incart .= '<div class="uk-margin-right"><img src="' . $item['product']->images->first->size(60)->url . '" alt="' . $item['product']->title . '"></div>';
		$incart .= '<div class="uk-text-left">';
		$incart .= '<div class="uk-text-uppercase">' . $item['product']->title . '</div>';
		$incart .= '<div class="uk-text-uppercase uk-text-small uk-text-muted" id="size-' . $item['product']->id . '" data-size="' . $item['size']->id . '">Размер: ' . $item['size']->title . '</div>';
		// $incart .= '<div class="uk-text-small uk-text-muted uk-margin-remove">' . $item->size->body . '</div>';
		$incart .= '</div>';
		$incart .= '</a>';
		$incart .= '</td>';
		$incart .= '<td id="price-' . $item['product']->id . '" data-price="' . $item['price'] . '">';
		$incart .= formatMoney($item['price']);
		$incart .= '</td>';
		$incart .= '<td>';
		$incart .= '<div class="uk-flex uk-flex-row uk-flex-middle uk-flex-center">';
		$incart .= '<button class="uk-width-xsmall uk-button uk-button-default uk-button-small" name="minus" value="' . $item['product']->id . '"><span uk-icon="minus"></span></button>';
		$incart .= '<input id="qnt-' . $item['product']->id . '" class="uk-width-xsmall uk-input uk-text-center uk-form-small" type="number" name="qnt" value="' . $item['qnt'] . '">';
		$incart .= '<button class="uk-width-xsmall uk-button uk-button-default uk-button-small" name="plus" value="' . $item['product']->id . '"><span uk-icon="plus"></span></button>';
		$incart .= '</div>';
		$incart .= '</td>';
		$incart .= '<td id="sum-' . $item['product']->id . '" class="productsum" data-sum="' . $item['price'] * $item['qnt'] . '">';
		$incart .= formatMoney($item['price'] * $item['qnt']);
		$incart .= '</td>';
		$incart .= '<td>';
		$incart .= '<a href="./?remove=' . $item['product']->id . '"><span uk-icon="trash"></span></a>';
		$incart .= '</td>';
		$incart .= '</tr>';
		$tq = $tq + $item['qnt'];
		$total = $total + $item['price'] * $item['qnt'];
	}
	$incart .= '</tbody>';
	$incart .= '</table>';
} else {
	$incart .= '<div class="uk-text-center uk-text-muted">Корзина пуста</div>';
}


?>

<div id="content">
	<div class="uk-container uk-padding">
		<div class="uk-grid-divider" uk-grid>
			<div class="uk-width-expand">
				<h2 class="uk-text-uppercase uk-margin-bottom">Товары в корзине</h2>
				<?php
				if (count($cart_products) > 0) {
					echo $incart;
				} else {
					echo '<div class="uk-text-muted uk-text-center uk-margin-large">Товары в корзине отсутствуют...</div>';
				}
				?>

				<hr>
				<div class="uk-margin uk-flex uk-flex-center uk-flex-left@m">
					<div>
						<a class="uk-button uk-button-default uk-button-large" href="<?= $pages->get('/catalog/')->url ?>"><span uk-icon="chevron-left"></span> В каталог</a>
					</div>
				</div>
			</div>
			<div class="uk-width-1-3@m">
				<h3 class="uk-text-uppercase uk-heading-divider">Сумма Заказа:</h3>
				<div class="uk-grid-small" uk-grid>
					<div class="uk-width-expand" uk-leader>Всего наименований</div>
					<div><span id="tqnt"><?= $tq ?></span> шт.</div>
				</div>
				<div class="uk-grid-small" uk-grid>
					<div class="uk-width-expand" uk-leader>Стоимость</div>
					<div id="total"><?= formatMoney($total) ?></div>
				</div>
				<hr>
				<div class="uk-grid-small uk-text-uppercase uk-text-bold uk-text-secondary" uk-grid>
					<div class="uk-width-expand" uk-leader>Итого</div>
					<div id="vsego"><?= formatMoney($total + $delivery) ?></div>
				</div>
				<div class="uk-margin-medium uk-width-auto uk-text-center">
					<a href="/order/" class="uk-button uk-button-primary uk-button-large">Оформить заказ</a>
				</div>
			</div>
		</div>
	</div>
</div>