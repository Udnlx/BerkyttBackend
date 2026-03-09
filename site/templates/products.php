<?php

namespace ProcessWire;

$content = '';
$content .= $page->body;

$catalog = $page->children('template=category');
$list = '<h2 class="uk-text-uppecase uk-heading-divider">Каталог ' . $page->title . '</h2>';
$list .= '<div class="uk-child-width-1-3@m uk-grid-divider uk-flex-middle uk-flex-center uk-height" uk-grid uk-height-match="target: > a > .uk-card">';
foreach ($catalog as $category) {
    $list .= '<a href="' . $category->url . '">';
    $list .= '<div class="uk-card uk-card-small">';
    $list .= '<div class="uk-card-body">';
    $list .= '<h3 class="uk-card-title uk-text-center">' . $category->title . '</h3>';
    $list .= '</div>';
    if (count($category->images) > 0) {
        $list .= '<div class="uk-card-media-top uk-padding-small img-shadow">';
        $list .= '<img src="' . $category->images->first->url . '" alt="">';
        $list .= '</div>';
    }
    $list .= '</div>';
    $list .= '</a>';
}
$list .= '</div>';

$content .= $list;

?>

<div id="content">
    <div class="uk-container uk-padding">
        <?= $content; ?>
    </div>
    <?= renderBlocks($page) ?>
</div>