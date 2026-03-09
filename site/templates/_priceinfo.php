<?php

namespace ProcessWire;

require_once './_func.php';

$prices = getDiscountPrice($page);
$price = formatMoney($prices['price']);
$discount = $prices['discount'];
$discountprice = formatMoney($prices['total']);
$dr = $prices['discountrule'];

echo '<div>Базовая цена товара: <span class="uk-text-success uk-text-bold">' . $price . '</span></div>';
echo '<div>Цена товара с учетом скидок: <span class="uk-text-danger uk-text-bold">' . $discountprice . ' (скидка ' . $discount . '%)</span></div>';
echo '<div class="uk-text-small uk-text-muted">Для товара применяется правило:  ' . $dr . '</div>';
