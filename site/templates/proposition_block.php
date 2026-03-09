<?php

namespace ProcessWire;

$content = '';
$content .= '<section class="uk-section">';
$content .= '<div class="uk-container uk-padding">';
$content .= '<h2 class="uk-heading-divider">' . $page->headline . '</h2>';
$content .= '<div class="uk-grid-divider uk-child-width-1-3@m uk-margin-large-top" uk-grid>';
foreach ($page->block_text as $item) {
	$content .= '<div>';
	$content .= '<h3 class="uk-text-secondary">' . $item->title . '</h3>';
	$content .= $item->body;
	$content .= '</div>';
}
$content .= '</div>';
$content .= '</div>';
$content .= '</section>';

echo $content;
