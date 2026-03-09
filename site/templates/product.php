<?php

namespace ProcessWire;

$prices = getDiscountPrice($page);

$total = '';
$price = '';

$total = '<div class="uk-text-danger uk-text-normal uk-text-bold">' . formatMoney($prices['total']) . '</div>';
$price = '<div class="uk-text-small uk-text-strike">' . formatMoney($prices['price']) . '</div>';

$images = '';
$images_nav = '';
$i = 0;
if ($page->video) {
    $images .= '<li class="uk-text-center uk-padding-small">';
    $images .= '<a href="' . $page->video->url . '" alt="' . $page->title . '"><video autoplay muted loop playsinline style="height: 100%;"><source src="' . $page->video->url . '" type="video/mp4"></video></a>';
    $images .= '</li>';
    $images_nav .= '<li uk-slideshow-item="' . $i . '"><a href="#"><video muted loop playsinline style="width: 49px;"><source src="' . $page->video->url . '" type="video/mp4"></video></a></li>';
    $i = 1;
}
foreach ($page->images as $image) {
    $images .= '<li class="uk-text-center uk-padding-small">';
    $images .= '<a href="' . $image->size(0, 1080)->url . '" alt="' . $image->desc . '" data-caption="' . $page->title . '"><img class="uk-height-1-1" src="' . $image->size(0, 650)->url . '" alt="' . $image->desc . '"></a>';
    $images .= '</li>';
    $images_nav .= '<li uk-slideshow-item="' . $i . '"><a href="#"><img src="' . $image->size(100)->url . '" alt="' . $image->desc . '" width="60"></a></li>';
    $i++;
}

$same_models = '';
if ($page->same_models->count > 0) {
    $same_models .= '<h5 class="uk-text-uppercase">Другие цвета:</h5>';
    $same_models .= '<div class="uk-grid-small uk-flex-row" uk-grid>';
    foreach ($page->same_models as $item) {
        $color = 'Цвет не указан';
        if ($item->color) {
            $color = $item->color->title;
        }
        $same_models .= '<a class="uk-link-heading" href="' . $item->url . '" uk-tooltip="title:' . $color . ';pos: bottom;">';
        if (count($item->images) > 0) {
            $same_models .= '<img src="' . $item->images->first->size(150)->url . '" width="120">';
        } else {
            $same_models .= '<img src="' . $homepage->noimage->size(150)->url . '" width="120">';
        }
        $same_models .= '</a>';
    }
    $same_models .= '</div>';
}

$parameters = '';
if ($page->season) {
    $parameters .= '<tr>';
    $parameters .= '<td>' . $fields->get('name=season')->label . '</td>';
    $parameters .= '<td>' . $page->season->title . '</td>';
    $parameters .= '</tr>';
}
if ($page->gender) {
    $parameters .= '<tr>';
    $parameters .= '<td>' . $fields->get('name=gender')->label . '</td>';
    $parameters .= '<td>' . $page->gender->title . '</td>';
    $parameters .= '</tr>';
}
if ($page->age) {
    $parameters .= '<tr>';
    $parameters .= '<td>' . $fields->get('name=age')->label . '</td>';
    $parameters .= '<td>' . $page->age->title . '</td>';
    $parameters .= '</tr>';
}
if ($page->color) {
    $parameters .= '<tr>';
    $parameters .= '<td>' . $fields->get('name=color')->label . '</td>';
    $parameters .= '<td>' . $page->color->title . '</td>';
    $parameters .= '</tr>';
}
if ($page->pattern) {
    $parameters .= '<tr>';
    $parameters .= '<td>' . $fields->get('name=pattern')->label . '</td>';
    $parameters .= '<td>' . $page->pattern->title . '</td>';
    $parameters .= '</tr>';
}
if ($page->main_material->count > 0) {
    $parameters .= '<tr>';
    $parameters .= '<td>' . $fields->get('name=main_material')->label . '</td>';
    $main = '';
    foreach ($page->main_material as $material) {
        $main .= $material->fabric->title . ' - ' . $material->percentage . '%, ';
    }
    $parameters .= '<td>' . \substr($main, 0, -2) . '</td>';
    $parameters .= '</tr>';
}
if ($page->back_material->count > 0) {
    $parameters .= '<tr>';
    $parameters .= '<td>' . $fields->get('name=back_material')->label . '</td>';
    $back = '';
    foreach ($page->back_material as $material) {
        $back .= $material->fabric->title . ' - ' . $material->percentage . '%, ';
    }
    $parameters .= '<td>' . \substr($back, 0, -2) . '</td>';
    $parameters .= '</tr>';
}
if ($page->length) {
    $parameters .= '<tr>';
    $parameters .= '<td>' . $fields->get('name=length')->label . '</td>';
    $parameters .= '<td>' . $page->length . '</td>';
    $parameters .= '</tr>';
}

$editor = '';
if (wire('user')->isLoggedin() && $page->editable()) {
    $editor = '<a class="uk-button-link uk-margin-right" href="' . $page->editUrl() . '"><span uk-icon="icon:file-edit; ratio:1.8;"></span></a>';
}

//Comments
$comments = '';
// if ($page->comments->count > 0) {
//     foreach ($page->comments as $comment) {
//         if ($comment->status < 1) continue; // skip unapproved or spam comments
//         $cite = htmlentities($comment->cite); // make sure output is entity encoded
//         $text = htmlentities($comment->text);
//         $date = date('d.m.y', $comment->created); // format the date
//         $time = date('H:i', $comment->created);
//         $comments .= '<article class="uk-comment uk-margin">';
//         $comments .= '<header class="uk-comment-header">';
//         $comments .= '<div class="uk-grid-medium uk-flex-middle" uk-grid>';
//         $comments .= '<div class="uk-width-auto">';
//         $comments .= '<span uk-icon="icon:user; ratio:2;"></span>';
//         $comments .= '</div>';
//         $comments .= '<div class="uk-width-expand">';
//         $comments .= '<h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#">' . $cite . '</a></h4>';
//         $comments .= '<ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-remove-top">';
//         $comments .= '<li><a href="#">' . $date . '</a></li>';
//         $comments .= '<li><a href="#">' . $time . '</a></li>';
//         $comments .= '</ul>';
//         $comments .= '</div>';
//         $comments .= '</div>';
//         $comments .= '</header>';
//         $comments .= '<div class="uk-comment-body uk-margin-left">';
//         $comments .= '<p>' . $text . '</p>';
//         $comments .= '</div>';
//         $comments .= '</article>';
//         $comments .= '<hr class="uk-margin-remove">';
//     }
// } else {
//     $comments .= '<div class="uk-text-muted uk-text-italic uk-text-center"> Для этого товара еще никто не оставил отзыв.</div>';
// }

$sizes = '';

if ($page->sizes->count > 0) {
    $i = 0;
    $current_sizes = [];
    foreach ($page->sizes as $item) {
        $current_sizes[$item->size->russian_size][$item->id] = $item->size->title;
    }
    \ksort($current_sizes);
    $sizes .= '<table class="uk-table uk-table-small">';
    $sizes .= '<caption class="uk-margin-bottom">';
    $sizes .= '<div class="uk-flex uk-flex-row uk-flex-between">';
    $sizes .= '<div class="uk-text-uppercase uk-text-light">Размеры:</div>';
    if ($page->no_sizes_helper == false) {
        $sizes .= renderSizesHelper($page->gender);
    }
    $sizes .= '</div>';
    $sizes .= '</caption>';
    $sizes .= '<tbody>';
    foreach ($current_sizes as $key => $value) {
        \asort($value);
        $sizes .= '<tr>';
        $sizes .= '<td class="uk-border-right"><span class="uk-label" uk-tooltip="title: Российский размер">' . $key . '</span></td>';
        foreach ($value as $id => $size_title) {
            $i++;
            $size = $pages->get($id);
            if ($size->quantity && $size->quantity > 0) {
                $sizes .= '<td><label class="size"><input class="uk-radio uk-hidden" type="radio" name="size" value="' . $size->size->id . '"><span class="uk-text-danger" uk-tooltip="title: Размер производителя<br>В наличии: ' . $size->quantity . ' шт.">' . $size->size->title . '</span></label></td>';
            } else {
                $sizes .= '<td><div class="size"><span class="uk-text-muted uk-text-strike uk-disabled" uk-tooltip="title: Размер производителя">' . $size->size->title . '</span></div></td>';
            }
        }
        $sizes .= '</tr>';
    }
    $sizes .= '</tbody>';
    $sizes .= '</table>';
}

?>

<div id="content">
    <div class="uk-container uk-padding">
        <div class="uk-child-width-1-2@m" uk-grid>
            <div class="uk-position-relative">
                <?php
                if ($page->new) {
                    echo '<div class="uk-card-badge uk-label uk-label-danger uk-position-top-left uk-margin-top">Новинка</div>';
                }
                ?>
                <div uk-slideshow="animation: fade; ratio: false">
                    <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1">
                        <ul class="uk-slideshow-items uk-height-large" uk-lightbox>
                            <?= $images; ?>
                        </ul>
                    </div>
                    <hr class="uk-divider-icon">
                    <div class="uk-margin">
                        <ul class="uk-thumbnav uk-margin uk-flex-center">
                            <?= $images_nav; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <div>
                    <?= getBreadcrumbs($page) ?>
                </div>
                <h1 class="uk-heading-divider uk-margin"><?= $editor; ?><?= $page->title; ?></h1>
                <div class="uk-flex uk-flex-between">
                    <div>
                        <?php
                        if ($prices['discount'] > 0 && $prices['price'] !== 0) {
                            echo '<span class="uk-text-strike uk-margin-right">' . formatMoney($prices['price']) . '</span>';
                            echo '<span class="uk-text-danger uk-h2 uk-text-bold">' . formatMoney($prices['total']) . '</span>';
                        } else {
                            echo '<span class="uk-h2 uk-text-bold">' . formatMoney($prices['price']) . '</span>';
                        }
                        ?>

                    </div>
                </div>
                <div class="uk-margin-top">
                    <h5 class="uk-text-uppercase uk-text-light">Описание:</h5>
                    <?= $page->body; ?>
                </div>
                <?= $same_models; ?>
                <hr>
                <form action="/cart/" method="post">
                    <div class="uk-margin-top uk-grid-medium uk-flex-center uk-flex-between@m uk-flex-wrap" uk-grid>
                        <div class="uk-flex uk-flex-row uk-flex-middle">
                            <div id="minusqnt" class="uk-width-xsmall uk-button uk-button-default uk-button-small"><span uk-icon="minus"></span></div>
                            <input id="qnt" class="uk-width-xsmall uk-input uk-text-center uk-form-small" type="number" name="qnt" value="1">
                            <div id="plusqnt" class="uk-width-xsmall uk-button uk-button-default uk-button-small"><span uk-icon="plus"></span></div>
                        </div>
                        <div>
                            <button class="uk-button uk-button-secondary" type="submit" id="addtocart" name="addtocart" value="<?= $page->id ?>">Добавить в корзину</button>
                        </div>
                    </div>
                    <div id="error" class="uk-alert-danger uk-animation-slide-bottom-small" uk-alert hidden>
                        <p>Необходимо выбрать размер!</p>
                    </div>
                    <div class="uk-margin-top">
                        <?= $sizes; ?>
                    </div>
                    <div id="size-description" class="uk-text-small uk-text-muted uk-margin-remove" uk-alert hidden></div>
                </form>
            </div>
        </div>
        <div class="uk-margin-top">
            <ul uk-tab>
                <li><a href="#">Параметры</a></li>
                <!--
                <li><a href="#">Отзывы</a></li>
                -->
            </ul>

            <ul class="uk-switcher uk-margin">
                <li>
                    <table class="uk-table uk-text-left uk-table-small">
                        <tbody>
                            <?= $parameters; ?>
                        </tbody>
                    </table>
                </li>
                <!--
                <li>
                    <?= $comments; ?>
                    <div class="uk-flex uk-flex-center">
                        <div class="uk-width-1-2 uk-card uk-card-default uk-margin-large-top uk-card-body">
                            <?php
                            // echo $page->comments->renderForm(
                            //     array(
                            //         'headline' => "<h3>Мы будем признательны за ваш отзыв</h3>",
                            //         'successMessage' => "<p class='uk-text-success'>Спасибо, ваш отзыв отправлен.</p>",
                            //         'errorMessage' => "<p class='uk-text-danger'>Ваш отзыв не удалось отправить.</p>",
                            //         'processInput' => true,
                            //         'encoding' => 'UTF-8',
                            //         'labels' => array(
                            //             'cite' => 'Ваше имя',
                            //             'email' => 'Электронная почта',
                            //             'text' => 'Отзыв',
                            //             'submit' => 'Отправить',
                            //         )
                            //     )
                            // );
                            ?>
                        </div>
                    </div>
                </li>
                -->
            </ul>
        </div>
    </div>
</div>