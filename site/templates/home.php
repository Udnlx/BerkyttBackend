<?php

namespace ProcessWire;

$datenow = wireDate('Y-m-d');
$top_info_banner = '';
$top_info_banner_element = $pages->get("template=top_info_banner");
if ($top_info_banner_element->date_start && $top_info_banner_element->date_finish) {
    if ($datenow >= $top_info_banner_element->date_start && $datenow <= $top_info_banner_element->date_finish) {
		$top_info_banner = '
			<div class="infobanner">
					' . $top_info_banner_element->body . '
			</div>
		';
    }
}

$menu = '';
foreach ($homepage->main_menu as $item) {
    if ($item->hasChildren() && $item->template->name != 'wheretobuy') {
        $menu .= '<li class="uk-parent">';
    } else {
        if ($item == $page) {
            $menu .= '<li class="uk-active">';
        } else {
            $menu .= '<li>';
        }
    }
    $menu .= "<a href='{$item->url}'>{$item->title}</a>";
    if ($item->hasChildren() && $item->template->name != 'wheretobuy') {
        $menu .= '<ul class="uk-nav-sub">';
        foreach ($item->children as $child) {
            if ($child == $page) {
                $menu .= '<li class="uk-active">';
            } else {
                $menu .= '<li>';
            }
            $menu .= '<a href="' . $child->url . '">' . $child->title . '</a>';
            $menu .= '</li>';
        }
        $menu .= '</ul>';
    }
    $menu .= '</li>';
}

$content = '';

// $catalog = $pages->get('name=catalog')->children('template=category');
// $list = '<h2 class="uk-text-uppecase uk-heading-divider">Наш каталог</h2>';
// $list .= '<div class="uk-child-width-1-3@m uk-grid-divider uk-flex-middle uk-flex-center uk-height" uk-grid uk-height-match="target: > a > .uk-card">';
// foreach ($catalog as $category) {
// 	$list .= '<a href="' . $category->url . '">';
// 	$list .= '<div class="uk-card uk-card-small">';
// 	$list .= '<div class="uk-card-body">';
// 	$list .= '<h3 class="uk-card-title uk-text-center">' . $category->title . '</h3>';
// 	$list .= '</div>';
// 	$list .= '<div class="uk-card-media-top uk-padding-small img-shadow">';
// 	if (count($category->images) > 0) {
// 	    $list .= '<img src="' . $category->images->first->url . '" alt="">';
// 	}
// 	$list .= '</div>';
// 	$list .= '</div>';
// 	$list .= '</a>';
// }
// $list .= '</div>';

// $content .= $list;

?>

<!DOCTYPE html>
<html lang="<?php echo $language->title; ?>">

<head>
    <?php require 'inc/meta.php'; ?>
    <?php require 'inc/styles.php'; ?>
</head>

<body>
    <h1 class="uk-hidden"><?= $page->title; ?></h1>
    <section class="uk-section uk-section-transparent uk-section-xsmall uk-position-top uk-position-z-index">
        <?php echo $top_info_banner; ?>
        <div id="offcanvas-reveal" uk-offcanvas="mode: reveal; overlay: true">
            <div class="uk-offcanvas-bar uk-flex uk-flex-column uk-flex-between">
                <button class="uk-offcanvas-close" type="button" uk-close></button>
                <div>
                    <div class="uk-logo uk-margin-bottom">
                        <img src="<?= $homepage->logo_mono->url ?>" alt="<?= $homepage->title ?>">
                    </div>
                    <hr class="uk-margin-small">
                    <ul class="uk-nav-default uk-nav-parent-icon" uk-nav>
                        <?= $menu ?>
                    </ul>
                    <hr>

                </div>

                <div class='uk-grid-small uk-text-small uk-margin-top uk-text-bold' uk-grid>
                    <div>
                        <div class="uk-text-uppercase uk-heading-divider">Связаться с нами:</div>
                    </div>
                    <div>
                        <span uk-icon="icon: location; ratio: 0.8;"></span>
                        <span>
                            <?= $homepage->address; ?>
                        </span>
                    </div>
                    <div>
                        <span uk-icon="icon: mail; ratio: 0.8;"></span>
                        <a class="uk-text-small" href="mailto:<?= $homepage->email; ?>">
                            <?= $homepage->email; ?>
                        </a>
                    </div>
                    <div>
                        <span uk-icon="icon: receiver; ratio: 0.8;"></span>
                        <a class="uk-text-small uk-text-small" href="tel:<?= renderPhone($homepage->main_phone); ?>">
                            <?= $homepage->main_phone; ?>
                        </a>
                    </div>
                    <div>
                        <span uk-icon="icon: phone; ratio: 0.8;"></span>
                        <a class="uk-text-small" href="tel:<?= renderPhone($homepage->mobile_phone); ?>">
                            <?= $homepage->mobile_phone; ?>
                        </a>
                    </div>
                    <div>
                        <span uk-icon="icon: whatsapp; ratio: 0.8;"></span>
                        <a class="uk-text-small" href="https://wa.me/<?= renderPhone($homepage->whatsapp, false); ?>">
                            <?= $homepage->whatsapp; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class='uk-container uk-container-large'>
            <nav class="uk-navbar-container uk-navbar-transparent" uk-navbar>
                <div class="uk-navbar-left uk-width-1-2">
                    <ul class="uk-navbar-nav">
                        <li class="uk-animation-slide-left" style="width: 60px;height: 30px;"><a class="uk-navbar-toggle" uk-icon="icon:menu;ratio:1.5;" uk-toggle="target: #offcanvas-reveal" href="#"></a></li>
                    </ul>
                </div>
                <div class="uk-navbar-right uk-width-1-2 uk-flex-right">
                    <ul class="uk-navbar-nav uk-flex-right uk-visible@m">
                        <li><a class="uk-navbar-toggle" uk-search-icon="ratio:1.4;" href="#"></a></li>
                    </ul>
                    <div class="uk-navbar-dropdown uk-width-1-1 uk-position-center" uk-drop="mode: click; cls-drop: uk-navbar-dropdown; boundary: !nav">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-expand">
                                <form class="uk-search uk-search-navbar uk-width-1-1 uk-light" action="/search/" method="get">
                                    <input class="uk-search-input" type="search" placeholder="Поиск" name="q" autofocus>
                                </form>
                            </div>
                            <div class="uk-width-auto">
                                <a class="uk-navbar-dropdown-close" href="#" uk-close></a>
                            </div>
                        </div>
                    </div>
                    <ul class="uk-navbar-nav uk-flex-right uk-visible@m">
                        <li><?= renderAuth() ?></li>
                    </ul>
                    <ul class="uk-navbar-nav uk-flex-right">
                        <li><?= renderCart() ?></li>
                    </ul>
                </div>
            </nav>
        </div>
    </section>
    <?= renderBlocks($page) ?>
    <?php
    if ($content != '') {
        echo '<div class="uk-container uk-padding">';
        echo '<div id="content">';
        echo $content;
        echo '</div>';
        echo '</div>';
    }
    ?>
    <?php require 'inc/footer.php'; ?>
</body>

</html>