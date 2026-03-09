<?php

namespace ProcessWire;

$q = $sanitizer->text($input->get->q);
$matches = false;

if ($q) {
	$input->whitelist('q', $q);
	$q = $sanitizer->selectorValue($q);
	$selector = "template=product, title|body|ksu~=$q, limit=" . $limit;
	$matches = $pages->find($selector);
	$content = '';
	if ($matches->count) {
		$content .= '<div class="uk-text-muted uk-text-right uk-margin">Мы нашли ' . $matches->getTotal() . ' ' . getPosition($matches->count) . ', которые могут вам подойти</div>';
		$content .= '<div class="uk-child-width-1-3@m" uk-grid uk-height-match="target: > div > a > .uk-card > .uk-card-body">';
		foreach ($matches as $item) {
			$prices = getDiscountPrice($item);
			$content .= '<div>';
			$content .= '<a href="' . $item->url . '" class="uk-link-toggle">';
			$content .= '<div class="uk-card uk-card-default uk-card-hover  uk-card-small">';
			$content .= '<div class="uk-card-media-top uk-padding-small uk-text-center uk-height-medium">';
			$content .= '<img class="uk-height-1-1" src="' . $item->images->first->url . '" alt="">';
			$content .= '</div>';
			$content .= '<div class="uk-card-body">';
			if ($prices['discount'] > 0) {
				$content .= '<div class="uk-card-badge uk-label uk-label-danger uk-position-top-right uk-margin-top">-' . $prices['discount'] . ' %</div>';
			}
			$content .= '<div class="uk-card-title uk-text-center">' . $item->title . '</div>';
			$content .= '</div>';
			$content .= '<div class="uk-card-footer">';
			$content .= '<div class="uk-flex uk-flex-between uk-flex-middle">';
			$content .= '<div>';
			if ($prices['discount'] > 0) {
				$content .= '<div class="uk-text-small uk-text-strike">' . formatMoney($prices['price']) . '</div>';
			}
			$content .= '</div>';
			$content .= '<div class="uk-text-right">';
			if ($prices['discount'] > 0) {
				$content .= '<div class="uk-text-danger uk-text-normal uk-text-bold">' . formatMoney($prices['total']) . '</div>';
			} else {
				$content .= '<div class="uk-text-normal uk-text-bold">' . formatMoney($prices['total']) . '</div>';
			}
			$content .= '</div>';
			$content .= '</div>';
			$content .= '</div>';
			$content .= '</div>';
			$content .= '</a>';
			$content .= '</div>';
		}
		$content .= '</div>';
	} else {
		$content = "<h2>извините, но мы не смогли ничего найти.</h2>";
	}
} else {
	$content = '<p class="uk-text-muted uk-text-small">Пожалуйста, введите название товара, которое хотите найти.</p>';
}

?>

<div id="content">
	<div class="uk-container uk-padding">
		<div class="uk-width-expand">
			<form class="uk-search uk-search-large uk-border-bottom uk-width-1-1" action="./" method="get">
				<span uk-search-icon></span>
				<input class="uk-search-input" type="search" placeholder="Поиск" name="q" autofocus>
			</form>
		</div>
		<div class="uk-margin">
			<?= $content; ?>
		</div>
		<div class="uk-margin-medium-top uk-flex uk-flex-right">
			<?php if ($matches) echo renderUKPager($matches); ?>
		</div>
	</div>
</div>