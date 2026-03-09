<?php

namespace ProcessWire;

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
$content .= '<h1 class="uk-heading-hero" style="color: #ffffff; font-size: 53px;">' . $page->title . '</h1>';
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
$content .= '<a class="uk-button uk-button-default uk-text-white" href="http://berkytt.ru/catalog/coat/">Для мужчин</a>';
$content .= '</div>';

$content .= '<div class="uk-text-center">';
$content .= '<a class="uk-button uk-button-default uk-text-white" href="http://berkytt.ru/women-catalog/raincoats/">Для женщин</a>';
$content .= '</div>';

$content .= '</div>';

$content .= '</div>';
$content .= '</div>';
$content .= '</div>';

?>

<div id="content">
	<?php echo $content; ?>
</div>