<?php

namespace ProcessWire;

// $left_menu = '';
// $left_menu .= '<h3 class="uk-h3 uk-text-uppercase uk-text-white uk-text-right@m">' . $page->left_menu->title . '</h3>';
// $left_menu .= '<ul class="uk-list uk-text-small uk-text-right@m">';
// foreach ($page->left_menu->children as $item) {
//     if ($item->hasChildren()) {
//         $left_menu .= '<li class="uk-parent">';
//     } else {
//         if ($item == $page) {
//             $left_menu .= '<li class="uk-active">';
//         } else {
//             $left_menu .= '<li>';
//         }
//     }
//     if ($item->new) {
//         $left_menu .= '<a class="uk-link" href="' . $item->url . '"><div class="uk-flex uk-flex-middle"><div class="uk-text-uppercase uk-text-white">' . $item->title . '</div> <div class="uk-margin-bottom"><span class="uk-label uk-label-danger uk-margin-left">Новинка</span></div></div></a>';
//     } else {
//         $left_menu .= '<a class="uk-link" href="' . $item->url . '"><div class="uk-text-uppercase uk-text-white">' . $item->title . '</div></a>';
//     }
//     $left_menu .= '</li>';
// }
// $left_menu .= '</ul>';

// $right_menu = '';
// $right_menu .= '<h3 class="uk-h3 uk-text-uppercase uk-text-white">' . $page->right_menu->title . '</h3>';
// $right_menu .= '<ul class="uk-list uk-text-small">';
// foreach ($page->right_menu->children as $item) {
//     if ($item->hasChildren()) {
//         $right_menu .= '<li class="uk-parent">';
//     } else {
//         if ($item == $page) {
//             $right_menu .= '<li class="uk-active">';
//         } else {
//             $right_menu .= '<li>';
//         }
//     }
//     if ($item->new) {
//         $right_menu .= '<a class="uk-link" href="' . $item->url . '"><div class="uk-flex uk-flex-middle"><div class="uk-text-uppercase uk-text-white">' . $item->title . '</div> <div class="uk-margin-bottom"><span class="uk-label uk-label-danger uk-margin-left">Новинка</span></div></div></a>';
//     } else {
//         $right_menu .= '<a class="uk-link" href="' . $item->url . '"><div class="uk-text-uppercase uk-text-white">' . $item->title . '</div></a>';
//     }
//     $right_menu .= '</li>';
// }
// $right_menu .= '</ul>';

$content = '';
$headline = highliteName($page->headline);
$body = highliteName($page->body);

$content .= '<div class="uk-cover-container" uk-height-viewport="offset-top: true">';
$content .= '<img src="' . $page->images->first->url . '" alt="berkytt background" uk-cover>';
$content .= '<div class="uk-overlay uk-overlay-primary uk-height-1-1 uk-position-absolute uk-width-expand"></div>';
$content .= '<div class="uk-position-center uk-width-1-2@m uk-padding">';
$content .= '<div class="uk-container-large uk-position-relative">';

$content .= '<div class="uk-text-center">';
if ($page->headline) {
    $content .= '<h2 class="uk-text-uppercase uk-h3 uk-text-white uk-text-center uk-text-shadow uk-margin-remove">' . $page->headline . '</h2>';
}
$content .= '<div>';
$content .= '<img src="' . $homepage->logo->url . '" alt="' . $page->title . '" width="700">';
$content .= '</div>';

$content .= '<div class="uk-grid-small uk-flex-center uk-text-white uk-child-width-1-2@m uk-margin-top uk-text-bold" uk-grid>';
$content .= '<div>';
$content .= '<span uk-icon="icon: location; ratio: 0.8;"></span>';
$content .= '<a href="https://yandex.ru/maps/-/CCU4RXs99C" class="uk-text-white uk-margin-small-left">' . $sanitizer->text($homepage->address) . '</a>';
$content .= '</div>';
$content .= '<div>';
$content .= '<span uk-icon="icon: mail; ratio: 0.8;"></span>';
$content .= '<a class="uk-link" href="mailto:' . $homepage->email . '">';
$content .= '<span class="uk-text-white uk-margin-small-left">' . $homepage->email . '</span>';
$content .= '</a>';
$content .= '</div>';
$content .= '</div>';

$content .= '<div class="uk-grid-small uk-flex-center uk-text-white uk-text-bold uk-child-width-1-3@m" uk-grid>';
$content .= '<div>';
$content .= '<span uk-icon="icon: receiver; ratio: 0.8;"></span>';
$content .= '<a class="uk-link" href="tel:' . renderPhone($homepage->main_phone) . '">';
$content .= '<span class="uk-text-white uk-margin-small-left">' . $homepage->main_phone . '</span>';
$content .= '</a>';
$content .= '</div>';
$content .= '<div>';
$content .= '<span uk-icon="icon: phone; ratio: 0.8;"></span>';
$content .= '<a class="uk-link" href="tel:' . renderPhone($homepage->mobile_phone) . '">';
$content .= '<span class="uk-text-white uk-margin-small-left">' . $homepage->mobile_phone . '</span>';
$content .= '</a>';
$content .= '</div>';
$content .= '<div>';
$content .= '<span uk-icon="icon: whatsapp; ratio: 0.8;"></span>';
$content .= '<a class="uk-link" href="https://wa.me/' . renderPhone($homepage->whatsapp, false) . '">';
$content .= '<span class="uk-text-white uk-margin-small-left">' . $homepage->whatsapp . '</span>';
$content .= '</a>';
$content .= '</div>';
$content .= '</div>';
$content .= '<hr class="uk-divider-icon">';
$content .= '</div>';

$content .= '<div class="uk-grid-large uk-flex-center uk-child-width-1-2@m uk-margin-large-top" uk-grid>';

$content .= '<div class="uk-text-center">';
// $content .= $left_menu;
$content .= '<a class="uk-button uk-button-default uk-text-white" href="' . $page->left_menu->children()->first->httpUrl() . '">' . $page->left_menu->title . '</a>';
$content .= '</div>';

$content .= '<div class="uk-text-center">';
// $content .= $right_menu;
$content .= '<a class="uk-button uk-button-default uk-text-white" href="' . $page->right_menu->children()->first->httpUrl() . '">' . $page->right_menu->title . '</a>';
$content .= '</div>';

$content .= '</div>';

$content .= '</div>';
$content .= '</div>';
$content .= '<div class="uk-position-bottom-center uk-margin-large-bottom uk-light"><span class="uk-animation-slide-top" uk-icon="icon:chevron-down; ratio:2;"></span></div>';
$content .= '</div>';

echo $content;
