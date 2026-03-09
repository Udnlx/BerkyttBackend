<?php

namespace ProcessWire;

$parent = $page->parent();

$catalog = $parent->children('template=category');
$list = '<h3 class="uk-text-uppecase uk-heading-divider">' . $page->parent->title . '</h3>';
$list .= '<ul class="uk-list">';
foreach ($catalog as $category) {
    if ($category->id == $page->id && $input->get('onlynew', 'bool') !== true) {
        $list .= '<li class="uk-background-secondary uk-text-white">';
        $list .= '<a class="uk-link-text uk-margin-small-left" href="' . $category->url . '">' . $category->title . '</a>';
        $list .= '</li>';
    } else {
        $list .= '<li>';
        $list .= '<a class="uk-link-text" href="' . $category->url . '">' . $category->title . '</a>';
        $list .= '</li>';
    }
}
$list .= '</ul>';

$active_name = '';
$active_new = '';
$active_popular = '';

switch ($input->get->text('sort')) {
    case 'new':
        $sort = '-new';
        $sort_title = 'Новинки';
        $active_new = ' class="uk-active"';
        break;
    case 'popular':
        $sort = '-phits';
        $sort_title = 'По популярности';
        $active_popular = ' class="uk-active"';
        break;
    default:
        $sort = 'title';
        $sort_title = 'По названию';
        $active_name = ' class="uk-active"';
        break;
}

$filter = ', parent|group=' . $page->id;
$params = [];

if ($input->get('size', 'text')) {
    $filter .= ', sizes.size=' . $input->get('size', 'text');
    $params['size'] = $input->get('size', 'text');
}

if ($input->get('color', 'text')) {
    $filter .= ', color=' . $input->get('color', 'text');
    $params['color'] = $input->get('color', 'text');
}

if ($input->get('onlynew', 'bool') == true) {
    $gender = '';
    if ($input->get('gender', 'int')) {
        $gender = ', gender=' . $input->get('gender', 'int');
        $params['gender'] = $input->get('gender', 'int');
    }
    $filter = ', new=1' . $gender;
    $limit = 0;
    $params['onlynew'] = $input->get('onlynew', 'bool');
}

$all_products = $pages->find('template=product' . $filter . ', sort=' . $sort);

if ($input->get('size', 'text')) {
    $products = $all_products->find('sizes.quantity!=0, sort=' . $sort);
    $total_items = $products->getTotal();
} else {
    $products = $pages->find('template=product' . $filter . ', sort=' . $sort . ', limit=' . $limit);
    $total_items = $all_products->getTotal();
}

$items = '<div class="uk-flex uk-flex-between uk-flex-middle">';
$items .= '<div class="uk-text-muted">Мы нашли: ' . $total_items . ' ' . getPosition($total_items) . '</div>';
$items .= '<div>';
$items .= '<span class="uk-text-muted uk-text-small uk-margin-right">сортировка: ' . $sort_title . '</span>';
$items .= '<button class="uk-button uk-button-default uk-button-small" type="button"><span class="uk-margin-small-right" uk-icon="grid"></span>Сортировать</button>';
$items .= '<div uk-dropdown>';
$items .= '<ul class="uk-nav uk-dropdown-nav">';
$items .= '<li' . $active_name . '><a href="./?sort=name">По названию</a></li>';
$items .= '<li' . $active_new . '><a href="./?sort=new">Новинки</a></li>';
$items .= '<li' . $active_popular . '><a href="./?sort=popular">По популярности</a></li>';
$items .= '</ul>';
$items .= '</div>';
$items .= '</div>';
$items .= '</div>';
$items .= '<hr>';
$items .= '<div class="uk-grid-small uk-child-width-1-3@m" uk-grid uk-height-match="target: > div > a > .uk-card > .uk-card-body">';

$available_sizes = [];
foreach ($products as $item) {
    $quantity = 0;
    foreach ($item->sizes as $size) {
        if (intval($size->quantity) > 0) {
            $quantity = $quantity + \intval($size->quantity);
            if (!isset($available_sizes[$size->size->id])) {
                $available_sizes[$size->size->id] = $size->size->title;
            }
        }
    }
    $prices = getDiscountPrice($item);
    $items .= '<div>';
    $items .= '<a href="' . $item->url . '" class="uk-link-toggle">';
    $items .= '<div class="uk-card uk-card-default uk-card-hover  uk-card-small">';
    $items .= '<div class="uk-card-media-top uk-padding-small uk-text-center uk-height-medium">';
    if ($item->images->first) {
        $items .= '<img class="uk-height-1-1" src="' . $item->images->first->size(0, 270)->url . '" alt="">';
    } else {
        $items .= '<img class="uk-height-1-1" src="' . $homepage->noimage->size(0, 270)->url . '" alt="">';
    }
    $items .= '</div>';
    $items .= '<div class="uk-card-body">';
    if ($item->new) {
        $items .= '<div class="uk-card-badge uk-position-top-left uk-border-pill new">New</div>';
    }
    if ($quantity == 0) {
        $items .= '<div class="uk-card-badge uk-label uk-label-default uk-position-top-right uk-margin-top">' . __('Нет на складе') . '</div>';
    } else {
        if ($prices['discount'] > 0) {
            $items .= '<div class="uk-card-badge uk-label uk-label-danger uk-position-top-right uk-margin-top">-' . $prices['discount'] . ' %</div>';
        }
    }
    $items .= '<div class="uk-card-title uk-text-center">' . $item->title . '</div>';
    $items .= '</div>';
    $items .= '<div class="uk-card-footer">';
    $items .= '<div class="uk-flex uk-flex-between uk-flex-top">';
    $items .= '<div class="uk-text-large">Цена:</div>';
    $items .= '<div class="uk-text-right uk-text-bold">';
    if ($prices['discount'] > 0) {
        $items .= '<div class="uk-text-danger uk-text-large uk-text-bold">' . formatMoney($prices['total']) . '</div>';
        $items .= '<div class="uk-text-small uk-text-strike uk-text-center">' . formatMoney($prices['price']) . '</div>';
    } else {
        $items .= '<div class="uk-text-large uk-text-bold">' . formatMoney($prices['price']) . '</div>';
    }
    $items .= '</div>';
    $items .= '</div>';
    $items .= '</div>';
    $items .= '</div>';
    $items .= '</a>';
    $items .= '</div>';
}
$items .= '</div>';


$colors = [];
foreach ($all_products as $item) {
    if ($item->color instanceof Page) {
        if (isset($colors[$item->color->id])) {
            $colors[$item->color->id] = $colors[$item->color->id] + 1;
        } else {
            $colors[$item->color->id] = 1;
        }
    }
}

$content = '<div class="uk-grid-divider" uk-grid>';
$content .= '<div class="uk-width-1-4@m">';
$content .= $list;
if (count($colors) > 0) {
    $content .= '<h3 class="uk-heading uk-heading-divider">Цвета</h3>';
    $content .= '<ul class="uk-list">';
    foreach ($colors as $key => $value) {
        $content .= '<li>';
        $content .= '<div class="uk-flex uk-flex-between uk-flex-middle">';
        $color = $pages->get('id=' . $key);
        if ($input->get('color', 'text') == $key) {
            $content .= '<a class="uk-link" href="' . $page->httpUrl . '?color=' . $key . '">' . $color->title . '</a>';
        } else {
            $content .= '<a class="uk-link-text" href="' . $page->httpUrl . '?color=' . $key . '">' . $color->title . '</a>';
        }
        $content .= '<span class="uk-label">' . $value . '</span>';
        $content .= '</div>';
        $content .= '</li>';
    }
    $content .= '<li>';
    $content .= '<hr>';
    $content .= '</li>';
    $content .= '<li>';
    $content .= '<a class="uk-link-text" href="' . $page->httpUrl . '">Любой цвет</a>';
    $content .= '</li>';
    $content .= '</ul>';
}

// if (count($available_sizes) > 0) {
//     $content .= '<h3 class="uk-heading uk-heading-divider">Размеры</h3>';
//     $content .= '<div class="uk-grid-small" uk-grid>';
//     foreach ($available_sizes as $s_id => $s_title) {
//         $content .= '<div>';
//         if ($input->get('size', 'text') == $s_id) {
//             $content .= '<div class="uk-label uk-background-secondary uk-text-white">';
//             $content .= '<a class="uk-link-reset" href="' . $page->httpUrl . '?size=' . $s_id . '">' . $s_title . '</a>';
//             $content .= '</div>';
//         } else {
//             $content .= '<div class="uk-label">';
//             $content .= '<a class="uk-link-reset" href="' . $page->httpUrl . '?size=' . $s_id . '">' . $s_title . '</a>';
//             $content .= '</div>';
//         }
//         $content .= '</div>';
//     }
//     $content .= '</div>';
//     $content .= '<hr>';
//     $content .= '<div>';
//     $content .= '<a class="uk-link-text" href="' . $page->httpUrl . '">Любой размер</a>';
//     $content .= '</div>';
// }

$content .= '</div>';
$content .= '<div class="uk-width-3-4@m">';
$content .= $items;
$content .= '</div>';
$content .= '</div>';

?>

<div id="content">
    <div class="uk-container uk-padding">
        <?= $content; ?>
        <div class="uk-margin-medium-top uk-flex uk-flex-right">
            <?php
            echo renderUKPager($products, $params);
            ?>
        </div>
    </div>
</div>