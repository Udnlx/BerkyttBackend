<?php

namespace ProcessWire;

?>
<!DOCTYPE html>
<html lang="<?php echo $language->title; ?>">

<head>
	<?php require 'inc/meta.php'; ?>
	<?php require 'inc/styles.php'; ?>
</head>

<body>
	<?php require 'inc/header.php'; ?>
	<div uk-height-viewport="expand: true">
		<div id="content"></div>
	</div>
	<?php require 'inc/footer.php'; ?>
</body>

</html>