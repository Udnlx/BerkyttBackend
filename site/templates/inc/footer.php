<?php

namespace ProcessWire;

$menu = '';
foreach ($homepage->footer_menu as $item) {
    if ($item == $page) {
        $menu .= '<li class="uk-active">';
    } else {
        $menu .= '<li>';
    }
    $menu .= "<a href='{$item->url}'>{$item->title}</a>";
    $menu .= '</li>';
}

$popup_coockies_page = $pages->get('template=basic-page, name=about-cookies')

?>

<footer id="footer" class='uk-section uk-padding-small uk-section-primary uk-light uk-preserve'>
    <div class='uk-container uk-margin-top'>
        <div class="uk-flex-top" uk-grid>
            <div class='uk-width-3-5@s uk-text-small'>
                <h3 class="uk-text-uppercase">
                    <?= $homepage->title; ?>
                </h3>
                <div class="uk-margin-bottom">
                    <span class="uk-margin-small-right" uk-icon="clock"></span><span><?= $homepage->working_hours; ?></span>
                </div>
                <div>
                    <?= $homepage->footer_slogan; ?>
                </div>
                <hr>
                <div>
                    <ul class="uk-grid-small uk-flex-row uk-flex-middle" uk-grid>
                        <?php
                        echo $menu;
                        foreach ($homepage->payment_cards as $item) {
                            if ($item->logo_mono) {
                                echo '<li><img src="' . $item->logo_mono->url . '" alt="' . $item->title . '" width="36" uk-svg></li>';
                            } else {
                                echo '<li class="uk-text-small">' . $item->title . '</li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div id="contacts" class='uk-width-2-5@s'>
                <div class="uk-flex uk-flex-column uk-text-small">
                    <div class='uk-grid-small uk-flex-row-reverse' uk-grid>
                        <span uk-icon="location"></span>
                        <a href="https://yandex.ru/maps/-/CCU4RXs99C">
                            <?= $homepage->address; ?>
                        </a>
                    </div>
                    <div class='uk-grid-small uk-flex-middle uk-flex-row-reverse' uk-grid>
                        <span uk-icon="receiver"></span>
                        <a class="uk-link-text" href="tel:<?= renderPhone($homepage->main_phone); ?>">
                            <?= $homepage->main_phone; ?>
                        </a>
                    </div>
                    <div class='uk-grid-small uk-flex-middle uk-flex-row-reverse' uk-grid>
                        <span uk-icon="phone"></span>
                        <a class="uk-link-text" href="tel:<?= renderPhone($homepage->mobile_phone); ?>">
                            <?= $homepage->mobile_phone; ?>
                        </a>
                    </div>
                    <div class='uk-grid-small uk-flex-middle uk-flex-row-reverse' uk-grid>
                        <span uk-icon="whatsapp"></span>
                        <a class="uk-link-text" href="https://wa.me/<?= renderPhone($homepage->whatsapp, false); ?>">
                            <?= $homepage->whatsapp; ?>
                        </a>
                    </div>
                    <div class='uk-grid-small uk-flex-middle uk-flex-row-reverse' uk-grid>
                        <span uk-icon="mail"></span>
                        <a class="uk-link-text" href="mailto:<?= $homepage->email; ?>">
                            <?= $homepage->email; ?>
                        </a>
                    </div>
                </div>
                <div class="uk-flex uk-flex-right uk-margin-top uk-text-small uk-text-center">
                    <div><?= $homepage->copyright ?>1997 - <?= datetime('Y') ?> © Интерстиль плюс</div>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-container uk-margin-small">

    </div>
</footer>

<?php if (empty($_COOKIE['berkytt_messages_cookies'])) : ?>
	<div id="cookies" class="uk-cookies hide-cookies">
        <div class="uk-cookies-text">
            <?= $popup_coockies_page->body; ?>
        </div>
		<div class="uk-cookies-link">
			<a class="uk-button uk-button-secondary uk-cookies-link-acept">Принять</a>
			<a class="uk-button uk-button-primary uk-cookies-link-learn" href="/dokumenty/soglashenie-na-ispol-zovanie-kuki/">Информация</a>
		</div>
		<img class="uk-cookies-close" src="<?php echo $config->urls->assets; ?>images/close.svg" alt="" uk-img>
	</div>
<?php endif; ?>

<?php echo $footer_scripts; ?>

<!-- Снег на сайте -->
<script>
    new Snow ({
        iconColor: '#ffffff',
        iconSize: 15,
        showSnowBalls: false,
        showSnowBallsIsMobile: true,
        showSnowflakes: true,
        countSnowflake: 100,
        snowBallsLength: 10,
        snowBallIterations: 40,
        snowBallupNum: 1,
        snowBallIterationsInterval: 1000,
        clearSnowBalls: 20000,
    });
</script>
<!-- Снег на сайте -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(64632397, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true,
        ecommerce:"dataLayer"
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/64632397" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->