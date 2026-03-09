<?php

namespace ProcessWire;

$header_scripts .= '<script src="' . $config->urls->templates . 'assets/amcharts4/core.js"></script>';
$header_scripts .= '<script src="' . $config->urls->templates . 'assets/amcharts4/maps.js"></script>';
$header_scripts .= '<script src="' . $config->urls->templates . 'assets/geodata/russiaCrimeaLow.js"></script>';
$header_scripts .= '<script src="' . $config->urls->templates . 'assets/amcharts4/themes/animated.js"></script>';
$header_scripts .= '<script src="' . $config->urls->templates . 'assets/amcharts4/lang/ru_RU.js"></script>';
$header_scripts .= '<script src="' . $config->urls->templates . 'assets/geodata/lang/RU.js"></script>';



$content = '<h2>Партнеры</h2>';
$content .= '<div class="uk-child-width-1-2@s uk-grid-match" uk-grid>';
foreach ($page->partners as $item) {
	$content .= '<a class="uk-link-reset" href="' . $item->link . '">';
	$content .= '<div class="uk-card uk-card-hover uk-card-media-top uk-flex uk-flex-center uk-flex-middle">';
	$content .= '<img src="' . $item->logo->url . '" alt="' . $item->title . '">';
	$content .= '</div>';
	$content .= '</a>';
}
$content .= '</div>';
$content .= '<h2>Магазины</h2>';
$content .= '<ul class="uk-margin" uk-accordion>';
$data = '';
foreach ($page->children as $filial) {
	$content .= '<li id="city-' . $filial->id . '">';
	$content .= '<a class="uk-accordion-title" href="#"><span class="uk-text-uppercase">' . $filial->title . '</span></a>';
	$content .= '<div class="uk-accordion-content">';
	$content .= '<dl class="uk-description-list">';
	foreach ($filial->children as $store) {
		$content .= '<div class="uk-flex uk-flex-middle uk-flex-between uk-margin">';
		$content .= '<div>';
		$content .= '<dt>' . $store->title . '</dt>';
		$content .= '<dd>';
		if ($store->address) {
			$content .= '<div>' . $store->address . '</div>';
		}
		if ($store->link) {
			$content .= '<a href="' . $store->link . '">' . $store->link . '</a>';
		}
		$content .= '</div>';
		$content .= '</dd>';
		if (count($store->images) > 0) {
			$content .= '<div class="uk-margin-right uk-card uk-card-hover" uk-lightbox>';
			$content .= '<a href="' . $store->images->first->url . '" data-caption="Caption">';
			$content .= '<img src="' . $store->images->first->url . '" width="150px" alt="">';
			$content .= '</a>';
			$content .= '</div>';
		}

		$content .= '</div>';
	}
	$content .= '</dl>';
	$content .= '</div>';
	$content .= '</li>';
	$data .= "
	{
		'title': '" . $filial->title . "',
		'latitude': " . $filial->latitude . ",
		'longitude': " . $filial->longitude . ",
		'url': '#city-" . $filial->id . "',
		'color': colorSet.next(),
		'animatecolor': '#FFFFFF'
	},
	";
}
$content .= '</ul>';
?>

<div id="content">
	<div id="chartdiv" class="uk-section uk-section-muted uk-visible@m" uk-height-viewport="expand"></div>
	<div class="uk-position-top-right uk-margin-large-right uk-margin-xxlarge-top uk-visible@m">
		<h1 class="uk-heading-divider"><?= $title; ?></h1>
	</div>
	<div class="uk-container uk-padding">
		<?= $content; ?>
	</div>
	<?= renderBlocks($page) ?>

	<script>
		am4core.ready(function() {
			am4core.useTheme(am4themes_animated);
			var chart = am4core.create("chartdiv", am4maps.MapChart);
			chart.geodata = am4geodata_russiaCrimeaLow;
			chart.language.locale = am4lang_ru_RU;
			chart.projection = new am4maps.projections.Miller();
			chart.geodataNames = am4geodata_lang_RU;
			chart.chartContainer.wheelable = false;
			chart.seriesContainer.draggable = true;
			chart.seriesContainer.resizable = true;
			chart.deltaLongitude = -10;
			chart.homeZoomLevel = 1.8;
			chart.homeGeoPoint = {
				latitude: 62,
				longitude: 100
			};
			chart.zoomControl = new am4maps.ZoomControl();
			chart.zoomControl.dx = -120;
			chart.zoomControl.dy = -60;
			var polygonSeries = chart.series.push(new am4maps.MapPolygonSeries());
			polygonSeries.useGeodata = true;
			var polygonTemplate = polygonSeries.mapPolygons.template;
			polygonTemplate.polygon.fillOpacity = 0.8;
			polygonTemplate.polygon.fill = am4core.color("#cd1c75");
			polygonTemplate.polygon.stroke = am4core.color("#a22363");
			var hs = polygonSeries.mapPolygons.template.states.create("hover");
			hs.properties.fill = am4core.color("#ffffff");
			hs.properties.stroke = am4core.color("#444444");
			var imageSeries = chart.series.push(new am4maps.MapImageSeries());
			imageSeries.mapImages.template.propertyFields.longitude = "longitude";
			imageSeries.mapImages.template.propertyFields.latitude = "latitude";
			imageSeries.mapImages.template.tooltipText = "{title}";
			imageSeries.mapImages.template.propertyFields.url = "url";
			var circle = imageSeries.mapImages.template.createChild(am4core.Circle);
			circle.radius = 6;
			circle.propertyFields.fill = "color";
			circle.nonScaling = true;
			var circle3 = imageSeries.mapImages.template.createChild(am4core.Circle);
			circle3.radius = 3;
			circle3.propertyFields.fill = "animatecolor";
			circle3.nonScaling = true;
			var circle2 = imageSeries.mapImages.template.createChild(am4core.Circle);
			circle2.radius = 6;
			circle2.propertyFields.fill = "animatecolor";
			circle2.events.on("inited", function(event) {
				animateBullet(event.target);
			})

			function animateBullet(circle) {
				var animation = circle.animate([{
					property: "scale",
					from: 1 / chart.zoomLevel,
					to: 5 / chart.zoomLevel
				}, {
					property: "opacity",
					from: 1,
					to: 0
				}], 2000, am4core.ease.circleOut);
				animation.events.on("animationended", function(event) {
					animateBullet(event.target.object);
				})
			}
			var colorSet = new am4core.ColorSet();
			imageSeries.data = [<?= $data; ?>];
		});
	</script>
	<style>
		#filter-id-79 {
			display: none;
		}
	</style>
</div>