<link rel="stylesheet" href="<?php echo $config->urls->templates; ?>assets/main.css?v=<?php echo uniqid(); ?>" />
<link rel="stylesheet" href="<?php echo $config->urls->templates; ?>assets/snow.css?v=<?php echo uniqid(); ?>" />
<script src="<?php echo $config->urls->templates; ?>assets/main.js?v=<?php echo uniqid(); ?>"></script>
<script src="<?php echo $config->urls->templates; ?>assets/snow.js?v=<?php echo uniqid(); ?>"></script>

<?php
$do_not_track_parent = $page->parents('do_not_track=1');
$do_not_track = $page->do_not_track;

if (!$do_not_track && $do_not_track_parent->count() == 0) {
	echo $homepage->google_analytics;
	echo $homepage->yandex_metrika;
}
echo $header_styles;
echo $header_scripts;
