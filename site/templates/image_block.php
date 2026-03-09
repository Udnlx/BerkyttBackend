<?php

namespace ProcessWire;

$content = '';
$content .= '<section class="uk-section uk-section-muted uk-position-relative">';
$content .= '<div class="uk-container">';
$content .= '<div class="uk-cover-container uk-position-right uk-width-1-3@m uk-height-1-1">';
if ($page->images->first) {
	$content .= '<img class="uk-text-secondary uk-heading-divider" src="' . $page->images->first->url . '" alt="' . $page->title . '" uk-cover>';
}
$content .= '</div>';
$content .= '<div class="uk-flex uk-flex-wrap uk-child-width-1-2@m uk-width-2-3@m uk-position-z-index uk-position-relative uk-overlay uk-overlay-default">';
foreach ($page->block_text as $item) {
	$content .= '<div class="uk-padding-small">';
	$content .= '<h3 class="uk-text-secondary uk-heading-divider">' . $item->title . '</h3>';
	$content .= $item->body;
	$content .= '</div>';
}
$content .= '</div>';
$content .= '</div>';
$content .= '</section>';

echo $content;
