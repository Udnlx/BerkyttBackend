<?php

namespace ProcessWire;

$content = '<h1 class="uk-heading-divider">' . $title . '</h1>';
$content .= highliteName($page->body);

$all_docs_links = '';
$docs_folder = $pages->get("template=docs");
$all_docs = $docs_folder->children();
foreach ($all_docs as $doc) {
    $all_docs_links .= '
        <a href="' . $doc->url . '"><span style="color:#990066"><strong>' . $doc->title . '</strong></span></a><br>
    ';
}
?>

<div id="content">
	<div class="uk-container uk-padding">
		<?= $content; ?>
        <h3>Наши документы</h3>
        <?= $all_docs_links; ?>
	</div>
	<?= renderBlocks($page) ?>
</div>