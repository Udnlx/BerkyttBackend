<?php

namespace ProcessWire;

function renderUKPager($items, $params = []) {
    $pager = wire('modules')->get('MarkupPagerNav');
    $options = [
        'nextItemLabel' => '<span uk-pagination-next></span>',
        'previousItemLabel' => '<span uk-pagination-previous></span>',
        'listMarkup' => "<ul class='uk-pagination uk-flex-center' uk-margin>{out}</ul>",
        'itemMarkup' => "<li class='{class}'>{out}</li>",
        'linkMarkup' => "<a class='uk-link-reset' href='{url}'><span>{out}</span></a>",
        'currentItemClass' => 'uk-background-primary uk-light uk-padding-remove-vertical'
    ];
    if (count($params) > 0) {
        $pager->setGetVars($params);
    }
    $out = $pager->render($items, $options);

    return $out;
}

function getBreadcrumbs($items, $current = false) {
    $out = '';
    if ($items === null) $items = page();
    if ($items instanceof Page) $items = $items->parents();
    if (!$items->count) return '';
    $home = wire('pages')->get('/');

    $out = "<ul class='uk-breadcrumb uk-margin-remove-top'>";
    foreach ($items as $item) {
        if ($item != $home) {
            $out .= "<li><a href='$item->url'>$item->title</a></li>";
        }
    }
    if ($current) {
        $page = $items->wire('page');
        $out .= "<li><span>$page->title</span></li>";
    }
    $out .= "</ul>";

    return $out;
}

function renderPhone($phone, $plus = true) {
    $phone_url = '';
    if ($plus) {
        $s = [' ', '(', ')', '-'];
    } else {
        $s = [' ', '(', ')', '-', '+'];
    }
    $phone_url = str_replace($s, '', $phone);
    return $phone_url;
}

function getDiscountPrice($page) {
    $price = 0;
    if ($page->price) {
        $price = $page->price;
    }
    $discount = 0;
    $dg = __('без скидки');
    if ($page->discount != 0) {
        $discount = $page->discount;
        $dg = __('скидка установлена для товара');
    } elseif ($page->parent->discount != 0) {
        $discount = $page->parent->discount;
        $dg = __('скидка установлена для группы товаров');
    }
    $total = $price;

    $price = (float) str_replace(',', '.', $price);
    $discount = (float) str_replace(['%', ','], ['', '.'], $discount);

    if ($discount != 0) {
        $total = $price - ($price * $discount / 100);
    }
    $out = [
        'price' => $price,
        'discount' => $discount,
        'total' => $total,
        'discountrule' => $dg
    ];

    return $out;
}

function formatMoney($price) {
    // $fmt = new \NumberFormatter('ru_RU', \NumberFormatter::CURRENCY);
    // $fmt->setTextAttribute($fmt::CURRENCY_CODE, 'RUB');
    // $fmt->setAttribute($fmt::FRACTION_DIGITS, 0);
    // if ($price != 0) {
    // 	return $fmt->format($price);
    // } else {
    // 	return $fmt->format(0);
    // }  . '₽'
    if ($price > 0) {
        return \number_format($price, 0, ',', ' ') . ' ₽';
    } else {
        return \number_format(0, 0, ',', ' ') . ' ₽';
    }
}

function getPosition($count) {
    $nune = 'позиция';
    if ($count == 1) {
        $nune = 'позицию';
    }
    if ($count > 1 && $count < 5) {
        $nune = 'позиции';
    }
    if ($count > 4 || $count == 0) {
        $nune = 'позиций';
    }
    return $nune;
}

function getProduct($count) {
    $nune = 'товар';
    if ($count == 1) {
        $nune = 'товар';
    }
    if ($count > 1 && $count < 5) {
        $nune = 'товара';
    }
    if ($count > 4 || $count == 0) {
        $nune = 'товаров';
    }
    return $nune;
}

function isInStock($page) {
    $out = '<span class="uk-text-danger">Нет в наличии</span>';
    $total = 0;
    foreach ($page->sizes as $size) {
        if ($size->quantity) {
            $total += $size->quantity;
        }
    }
    if ($total > 0) {
        $out = '<span class="uk-text-success">В наличии</span>';
    }
    return $out;
}

function renderBlocks($page) {
    $out = '';
    if ($page->hasField('blocks')) {
        foreach ($page->blocks as $block) {
            $out .= $block->render();
        }
    }

    return $out;
}

function highliteName($text = '') {
    if (\strpos($text, 'Berkytt') !== false) {
        $text = \str_replace('Berkytt', '<spann class="uk-text-secondary">Berkytt</spann>', $text);
    }
    return $text;
}

function renderSizesHelper($gender) {
    $helper = wire('pages')->get('sizes');
    if ($gender == '1') {
        $helper = wire('pages')->get('sizes');
    }
    if ($gender == '2') {
        $helper = wire('pages')->get('sizes-female');
    }
    $out = '';
    if (!$helper instanceof NullPage) {
        $out = '<a class="uk-link" href="#helper" uk-toggle>' . __('Как определить свой размер?') . '</a>';
        $out .= '<div id="helper" class="uk-modal-container" uk-modal>';
        $out .= '<div class="uk-modal-dialog">';
        $out .= '<button class="uk-modal-close-default" type="button" uk-close></button>';
        $out .= '<div class="uk-modal-header">';
        $out .= '<h2 class="uk-modal-title uk-text-light uk-text-uppercase">' . $helper->title . '</h2>';
        $out .= '</div>';
        $out .= '<div class="uk-modal-body uk-text-small uk-table-small uk-table-striped" uk-overflow-auto>';
        $out .= $helper->body;
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
    }
    return $out;
}

function getCart($session) {
    $cart = false;
    if ($session->get('cart') && $session->get('cart') instanceof Page) {
        $cart = $session->get('cart');
    }
    return $cart;
}

function renderCart() {
    $out = '<a href="' . wire('pages')->get('/cart/')->url . '">';
    $out .= '<div class="cart-info uk-flex uk-flex-middle uk-flex-nowrap uk-position-z-index">';

    $out .= '<span class="uk-h4 uk-margin-remove uk-text-white uk-text-uppercase">';
    $cart = wire('session')->get('cart');
    $card = '<div class="uk-margin-bottom uk-flex uk-flex-middle"><span class="uk-text-secondary uk-margin-small-right" uk-icon="icon: lock; ratio:2"></span><span class="uk-text-large">Корзина</span></div>';
    $card .= '<hr>';
    $total = 0;
    $tq = 0;
    if ($cart) {
        foreach ($cart['products'] as $item) {
            $total = $total + $item['price'] * $item['qnt'];
            $tq = $tq + $item['qnt'];
            $card .= '<div class="uk-grid-small" uk-grid>';
            $card .= '<div class="uk-width-expand" uk-leader>' . wire('pages')->get($item['product'])->title . ' (' . $item['qnt'] . 'шт.)</div>';
            $card .= '<div>' . formatMoney($item['price'] * $item['qnt']) . '</div>';
            $card .= '</div>';
        }
        $out .= formatMoney($total);
        $card .= '<hr>';
        $card .= '<div class="uk-grid-small uk-text-uppercase uk-h5" uk-grid>';
        $card .= '<div class="uk-width-expand" uk-leader>Итого</div>';
        $card .= '<div class="uk-text-secondary">' . formatMoney($total) . '</div>';
        $card .= '</div>';
        $card .= '<div class="uk-flex uk-flex-between uk-margin-medium-top">';
        $card .= '<a href="' . wire('pages')->get('template=cart')->url . '" class="uk-button uk-button-default uk-button-small">Перейти в корзину</a>';
        $card .= '<a href="/order/" class="uk-button uk-button-primary uk-button-small">Оформить заказ</a>';
        $card .= '</div>';
    } else {
        // $out .= __('Корзина');
        $card .= '<div class="uk-text-center uk-text-muted">Корзина пуста</div>';
    }
    $out .= '</span>';
    //$out .= '<span class="uk-margin-small-left">(' . $tq . ' ' . getProduct($tq) . ')</span>';
    $out .= '<div class="uk-position-relative">';
    $out .= '<span class="uk-text-secondary" uk-icon="icon: lock; ratio:2"></span>';
    if ($tq != 0) {
        $out .= '<span class="uk-position-bottom-center uk-label uk-label-danger">' . $tq . '</span>';
    }
    $out .= '</div>';
    $out .= '</div>';
    $out .= '</a>';
    $out .= '<div class="uk-width-large uk-text-small" uk-dropdown="pos: bottom-right">';
    $out .= $card;
    $out .= '</div>';
    return $out;
}


function renderAuth() {
    $out = '<a href="' . wire('pages')->get('/cabinet/')->url . '">';
    $out .= '<div class="uk-flex uk-flex-middle uk-flex-nowrap">';

    $out .= '<span class="uk-h4 uk-margin-remove uk-text-white uk-text-uppercase">';
    $out .= '</span>';
    // if (wire('user')->isLoggedin()) {
    $out .= '<span uk-icon="icon:user; ratio:1.4;"></span>';
    // } else {
    // 	$out .= '<span class="uk-margin-small-left">Вход / Регистрация</span>';
    // }
    $out .= '</div>';
    $out .= '</a>';

    return $out;
}

function getOrderStatus($order) {
    $out = '';
    $status = '';
    switch ($order->order_status) {
        case '2':
            $status = ' uk-label-warning';
            break;
        case '3':
            $status = ' uk-label-success';
            break;
        case '4':
            $status = ' uk-label-danger';
            break;
        default:
            $status = '';
            break;
    }
    $out = '<span class="uk-label' . $status . '">' . $order->order_status->title . '</span>';
    return $out;
}
