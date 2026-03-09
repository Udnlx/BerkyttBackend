<?php

namespace ProcessWire;

$content = '<h1 class="uk-heading-divider">' . $title . '</h1>';
$content .= highliteName($page->body);
?>

<div id="content">
	<div class="uk-container uk-padding">
		<?= $content; ?>
	</div>
	<?= renderBlocks($page) ?>
</div>