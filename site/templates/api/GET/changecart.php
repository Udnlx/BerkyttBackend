<?php

namespace ProcessWire;

if ($input->get('id', 'text')) {
	$p = $pages->get('template=product, id=' . $input->get('id', 'text'));
	if ($p->id) {
		$size = $pages->get('template=size, id=' . $input->get('size', 'text'));
		$add = $input->get('add', 'bool');
		$price = getDiscountPrice($p);
		if ($session->get('cart')) {
			$products = $session->get('cart')['products'];
			foreach ($products as $key => $value) {
				if ($products[$key]['product'] == $p->id && $products[$key]['size'] == $size->id) {
					if ($add) {
						$products[$key]['qnt'] = $products[$key]['qnt'] + 1;
					} else {
						$products[$key]['qnt'] = $products[$key]['qnt'] - 1;
					}
				}
			}
			$session->set('cart', ['products' => $products]);
		}
	} else {
		$result = setError('Такого товара не существует.', $result);
	}
} else {
	$result = setError('Неверно задан код товара.', $result);
}
