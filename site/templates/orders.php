<?php

namespace ProcessWire;

$sdek = $pages->get('/sdek/');

$cart = $session->get('cart');
if (!is_array($cart)) {
	$cart['products'] = [];
}
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
$delivery = 0;
if (count($cart_products) > 0) {
	foreach ($cart_products as $item) {
		$tq = $tq + $item['qnt'];
		$total = $total + $item['price'] * $item['qnt'];
	}
}

$user_details = '<div class="uk-text-muted uk-text-uppercase uk-text-light uk-heading-divider uk-margin-small">Информация о покупателе:</div>';
$user_details .= '<div class="uk-margin">';
if ($user->isLoggedin()) {
	if ($user->firstname) {
		$user_details .= '<div class="uk-margin-bottom">' . $user->lastname . ' ' . $user->firstname . ' ' . $user->middlename . '</div>';
	} else {
		$user_details .= '<div class="uk-margin">';
		$user_details .= '<div class="uk-inline uk-width-1-1">';
		$user_details .= '<span class="uk-form-icon" uk-icon="icon: user"></span>';
		$user_details .= '<input class="uk-input" name="fio" type="text" placeholder="Ваше имя" required></input>';
		$user_details .= '</div>';
		$user_details .= '</div>';
	}
	if ($user->email) {
		$user_details .= '<div class="uk-margin-bottom">email: ' . $user->email . '</div>';
	} else {
		$user_details .= '<div class="uk-margin uk-width-1-1">';
		$user_details .= '<div class="uk-inline">';
		$user_details .= '<a class="uk-form-icon" href="#" uk-icon="icon: mail"></a>';
		$user_details .= '<input class="uk-input" name="email" type="text" placeholder="Электронная почта" required>' . $user->email . '</input>';
		$user_details .= '</div>';
		$user_details .= '</div>';
	}

	if ($user->main_phone) {
		$user_details .= '<div class="uk-margin-bottom">Телефон: ' . $user->main_phone . '</div>';
	} else {
		$user_details .= '<div class="uk-margin">';
		$user_details .= '<div class="uk-inline uk-width-1-1">';
		$user_details .= '<span class="uk-form-icon" uk-icon="icon: receiver"></span>';
		$user_details .= '<input class="uk-input" type="text" name="phone" placeholder="Телефон" required>' . $user->phone . '</input>';
		$user_details .= '</div>';
		$user_details .= '</div>';
	}
} else {
	$user_details .= '<div class="uk-margin">';
	$user_details .= '<div class="uk-inline uk-width-1-1">';
	$user_details .= '<span class="uk-form-icon" uk-icon="icon: user"></span>';
	$user_details .= '<input class="uk-input" class="uk-input" name="fio" type="text" placeholder="Ваше имя" required></input>';
	$user_details .= '</div>';
	$user_details .= '</div>';

	$user_details .= '<div class="uk-margin">';
	$user_details .= '<div class="uk-inline uk-width-1-1">';
	$user_details .= '<span class="uk-form-icon" uk-icon="icon: mail"></span>';
	$user_details .= '<input class="uk-input" name="email" type="text" placeholder="Электронная почта" required></input>';
	$user_details .= '</div>';
	$user_details .= '</div>';

	$user_details .= '<div class="uk-margin">';
	$user_details .= '<div class="uk-inline uk-width-1-1">';
	$user_details .= '<span class="uk-form-icon" uk-icon="icon: receiver"></span>';
	$user_details .= '<input class="uk-input" type="text" name="phone" placeholder="Телефон" required></input>';
	$user_details .= '</div>';
	$user_details .= '</div>';
}
$user_details .= '</div>';

$user_details .= '<div class="uk-text-muted uk-text-uppercase uk-text-light uk-heading-divider uk-margin-small">Способ оплаты:</div>';
$user_details .= '<div class="uk-margin uk-child-width-expand uk-flex-column uk-grid">';
$checked = 'checked';
foreach ($pages->get('template=payment_types')->children as $item) {
	$user_details .= '<label class="uk-margin-small-bottom"><input class="uk-radio" type="radio" name="payment" value="' . $item->id . '" ' . $checked . '>  ' . $item->title . '</label>';
	if ($checked == 'checked') {
		$checked = '';
	}
}
$user_details .= '</div>';

$user_details .= '<div class="uk-text-muted uk-text-uppercase uk-text-light uk-heading-divider uk-margin-small">Доставка:</div>';
$user_details .= '<div class="uk-flex uk-flex-middle uk-flex-between uk-margin-bottom">';
// $user_details .= '<div>Адрес доставки: <span class="toggle-delivery">' . $user->address . '</span></div>';
// if ($user->address) {
// 	$user_details .= '<button class="uk-button uk-button-default uk-button-small" type="button" uk-toggle="target: .toggle-delivery; animation: uk-animation-fade">Изменить адрес</button>';
// 	$user_details .= '</div>';
// 	$user_details .= '<div class="uk-margin-bottom toggle-delivery" hidden>';
// } else {
$user_details .= '</div>';
$user_details .= '<div class="uk-margin-bottom">';
// }

$user_details .= '<div class="uk-child-width-expand uk-flex-column uk-grid">';
$checked = 'checked';
foreach ($pages->get('template=delivery_types')->children as $item) {
	if ($item == $pages->get('template=delivery, name=pickup')) {
		$did = ' id="pickup" ';
	} else {
		$did = ' ';
	}
	$user_details .= '<label class="uk-margin-small-bottom"><input' . $did . 'class="uk-radio" type="radio" name="delivery" value="' . $item->id . '" ' . $checked . '>  ' . $item->title . '</label>';
	$user_details .= '<div class="uk-text-small uk-text-muted uk-margin-small-bottom">' . $item->body . '</div>';
	if ($checked == 'checked') {
		$checked = '';
	}
}
$user_details .= '</div>';
$user_details .= '<div class="uk-margin-bottom">';
$user_details .= '<div id="deliveryAdress" class="uk-margin-small-bottom uk-text-muted"></div>';
$user_details .= '<div id="deliveryType" class="uk-margin-small-bottom uk-text-muted"></div>';
$user_details .= '</div>';
$user_details .= '<div class="toggle-address" hidden>';
$user_details .= '<div>';
$user_details .= '<a href="#modal-sdek" class="uk-button uk-button-default" uk-toggle>Выбрать адрес доставки</a>';
$user_details .= '</div>';
$user_details .= '<div id="forpvz" class="uk-width-auto"></div>';
$user_details .= '</div>';
$user_details .= '</div>';

$footer_scripts .= "<script>
const sum = " . round($total) . "
var goods = [";
foreach ($cart_products as $item) {
	for ($i = 0; $i < $item['qnt']; $i++) {
		$footer_scripts .= "
		{
			length: " . intval($sdek->length) . ",
			width: " . intval($sdek->width) . ",
			height: " . intval($sdek->height) . ",
			weight: " . intval($sdek->weight) . "
		},
		";
	}
}
$footer_scripts .= "
]
</script>\n";

$footer_scripts .= '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@cdek-it/widget@3" charset="utf-8"></script>';
$footer_scripts .= '
<div id="modal-sdek" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-width-1-1">
		<button class="uk-modal-close-outside" type="button" uk-close></button>
        <p class="uk-text-center" style="font-size:20px;font-weight:700">ВЫБОР ПУНКТА И СПОСОБА ДОСТАВКИ СДЭК</p>
        <div id="forpvzsdek" style="height:700px;"></div>
    </div>
</div>
';

?>

<div id="content">
	<div class="uk-container uk-padding">
		<form action="/order-confirmation/" method="post">
			<div class="uk-grid-divider" uk-grid>
				<div class="uk-width-expand">
					<h2 class="uk-text-uppercase uk-margin-bottom">Оформление заказа</h2>
					<?php
					echo $user_details;
					?>
				</div>
				<div class="uk-width-1-3@m">
					<div uk-sticky="offset: 150; bottom: true" style="z-index:1">
						<h3 class="uk-text-uppercase uk-heading-divider">Сумма Заказа:</h3>
						<div class="uk-grid-small" uk-grid>
							<div class="uk-width-expand" uk-leader>Всего наименований</div>
							<div id="cart_qnt"><?= $tq ?> шт.</div>
						</div>
						<div class="uk-grid-small" uk-grid>
							<div class="uk-width-expand" uk-leader>Стоимость</div>
							<div id="cart_price"><?= formatMoney($total) ?></div>
						</div>
						<div class="uk-grid-small" uk-grid>
							<div class="uk-width-expand" uk-leader>Доставка</div>
							<div id="deliveryPrice"><?= formatMoney($delivery) ?></div>
						</div>
						<hr>
						<div class="uk-grid-small uk-text-uppercase uk-text-bold uk-text-secondary" uk-grid>
							<div class="uk-width-expand" uk-leader>Итого</div>
							<div id="total"><?= formatMoney($total + $delivery) ?></div>
						</div>
						<div class="uk-margin-large uk-flex uk-flex-center uk-flex-column">
							<input type="text" name="address" value="" hidden />
							<textarea name="delivery_type" value="" hidden></textarea>
							<input type="text" name="delivery_price" value="" hidden />
							<?php
							echo $session->CSRF->renderInput();
							?>
							<div class="uk-flex uk-flex-center" style="margin: 0 0 10px 0;align-items: center;">
								<input class="uk-checkbox" type="checkbox" id="confirm" name="confirm">
								<label for="confirm" style="width: 100%;box-sizing: border-box;line-height: 1;margin: 0 0 0 10px;font-size: 13px;">
									Нажимая кнопку "Заказать", Вы принимаете <a href="/dokumenty/pol-zovatel-skoe-soglashenie/">Пользовательское соглашение</a> и даете согласие на обработку персональных данных
								</label>
							</div>
							<button id="dtn_order" class="uk-button uk-button-primary uk-button-large" name="neworder" type="submit" value="1" disabled>Заказать</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>