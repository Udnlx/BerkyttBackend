<?php

namespace ProcessWire;

$content = '';

if ($page->block_position == 1) {
    $content .= '<section class="uk-section uk-section-muted">';
    $content .= '<div class="uk-small uk-flex-wrap uk-child-width-1-2@m uk-position-relative" uk-grid>';
    $content .= '<div>';
    $content .= '<div class="uk-card">';
    if ($page->images->first) {
        $content .= '<div class="uk-card-media-top">';
        $content .= '<img class="uk-width-1-1" src="' . $page->images->first->url . '" alt="' . $page->title . '">';
        $content .= '</div>';
    }
    $content .= '<div class="uk-card-body uk-text-right">';
    $content .= '<h2 class="uk-text-right uk-margin-right uk-heading-divider uk-text-uppercase">' . $page->title . '</h2>';
    $content .= '<div class="uk-text-right uk-margin-right">' . $page->body . '</div>';
    $content .= '<div class="uk-text-right uk-margin-right"><a class="uk-button uk-button-secondary" href="' . $page->promo->url . '?onlynew=true&gender=1">Посмотреть новинки</a></div>';
    $content .= '</div>';
    $content .= '</div>';
    $content .= '</div>';

    $content .= '<div>';
    $content .= '<div class="uk-grid-small uk-flex-wrap uk-child-width-1-3@m" uk-grid>';
    $i = 0;
    foreach ($pages->find('template=product, parent|group=' . $page->promo . ', new=1, limit=3') as $item) {
        $content .= '<div>';
        $reverse = '';
        if ($i == 1) {
            $reverse = ' uk-flex-wrap-reverse@s';
        }
        $content .= '<div class="uk-grid-small uk-flex-wrap uk-width-expand' . $reverse . '" uk-grid>';

        $content .= '<div class="uk-card uk-card-default uk-padding uk-width-1-1">';
        $content .= '<div class="uk-card-media-top uk-text-center">';
        $content .= '<div class="uk-inline-clip uk-transition-toggle uk-height-medium" tabindex="0">';
        $content .= '<img class="uk-height-max-medium" src="' . $item->images->first->size(0, 450)->url . '" alt="' . $item->title . '">';
        $content .= '<img class="uk-transition-scale-up uk-position-cover" src="' . $item->images->eq(1)->size(0, 450)->url . '" alt="' . $item->title . '">';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="uk-card uk-card-default uk-padding uk-width-1-1">';
        $content .= '<div class="uk-card-title uk-height-medium uk-flex uk-flex-center uk-flex-middle">';
        $content .= '<div>';
        $content .= '<div class="uk-heading-divider">' . $item->title . '</div>';
        $content .= '<div class="uk-text-small uk-margin-small-top">' . $sanitizer->truncate($item->body, 200, 'sentence') . '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= ' </div>';

        $content .= ' </div>';

        $content .= ' </div>';
        $i++;
    }
    $content .= '</div>';
    $content .= '</div>';
    $content .= '</div>';
    $content .= '</section>';
} else {
    $content .= '<section class="uk-section uk-section-muted">';
    $content .= '<div class="uk-small uk-flex-wrap-reverse uk-child-width-1-2@m uk-position-relative" uk-grid>';
    $content .= '<div>';
    $content .= '<div class="uk-grid-small uk-flex-wrap uk-child-width-1-3@m" uk-grid>';
    $i = 0;
    foreach ($pages->find('template=product, parent|group=' . $page->promo . ', new=1, limit=3') as $item) {
        $content .= '<div>';
        $reverse = '';
        if ($i == 1) {
            $reverse = ' uk-flex-wrap-reverse@s';
        }
        $content .= '<div class="uk-grid-small uk-flex-wrap uk-width-expand' . $reverse . '" uk-grid>';

        $content .= '<div class="uk-card uk-card-default uk-padding uk-width-1-1">';
        $content .= '<div class="uk-card-media-top uk-text-center">';
        $content .= '<div class="uk-inline-clip uk-transition-toggle uk-height-medium" tabindex="0">';
        $content .= '<img class="uk-height-max-medium" src="' . $item->images->first->size(0, 450)->url . '" alt="' . $item->title . '">';
        $content .= '<img class="uk-transition-scale-up uk-position-cover" src="' . $item->images->eq(1)->size(0, 450)->url . '" alt="' . $item->title . '">';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="uk-card uk-card-default uk-padding uk-width-1-1">';
        $content .= '<div class="uk-card-title uk-height-medium uk-flex uk-flex-center uk-flex-middle">';
        $content .= '<div>';
        $content .= '<div class="uk-heading-divider">' . $item->title . '</div>';
        $content .= '<div class="uk-text-small uk-margin-small-top">' . $sanitizer->truncate($item->body, 200, 'sentence') . '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= ' </div>';

        $content .= ' </div>';

        $content .= ' </div>';
        $i++;
    }
    $content .= '</div>';
    $content .= '</div>';

    $content .= '<div>';
    $content .= '<div class="uk-card">';
    if ($page->images->first) {
        $content .= '<div class="uk-card-media-top">';
        $content .= '<img class="uk-width-1-1" src="' . $page->images->first->url . '" alt="' . $page->title . '">';
        $content .= '</div>';
    }
    $content .= '<div class="uk-card-body uk-text-left">';
    $content .= '<h2 class="uk-text-left uk-margin-right uk-heading-divider uk-text-uppercase">' . $page->title . '</h2>';
    $content .= '<div class="uk-text-left uk-margin-right">' . $page->body . '</div>';
    $content .= '<div class="uk-text-left uk-margin-right"><a class="uk-button uk-button-secondary" href="' . $page->promo->url . '?onlynew=true&gender=2">Посмотреть новинки</a></div>';
    $content .= '</div>';
    $content .= '</div>';
    $content .= '</div>';

    $content .= '</div>';
    $content .= '</section>';
}

echo $content;
