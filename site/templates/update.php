<?php

namespace ProcessWire;

include 'index.php';

$pDOStatement = $database->query('select `parent id`,`product SKU`,`post title`, `price`, `quantity` from update_products');
$result = $pDOStatement->fetchAll(\PDO::FETCH_ASSOC);

$count = 0;
$count_total = 0;
foreach ($result as $item) {
	if ($item['product SKU'] != '') {
		$parent = $pages->get('template=product, wp_id=' . $item['parent id']);
		$size = $pages->get('template=size, title=' . str_replace('-', '/', substr($item['post title'], -7)));
		$count++;
		if (!$parent->sizes->get('size=' . $size)) {
			echo '<p>' . $parent->title . ' = ' . $size->title . ' => ' . $item['quantity'] . '</p>';
			$sizes = $parent->sizes->getNew();
			$sizes->scancode = $item['product SKU'];
			$sizes->size = $size;
			$sizes->quntity = $item['quantity'];
			$sizes->save();
			$parent->sizes->add($sizes);
			$count_total++;
		} else {
			echo '<p>Замена количества ' . $parent->title . ' = ' . $size->title . ' => ' . $item['quantity'] . '</p>';
			$current_size = $parent->sizes->get('size=' . $size);
			$current_size->of(false);
			$current_size->quantity = $item['quantity'];
			$current_size->save();
		}
	}
}
echo $count . '<br>';
echo $count_total . '<br>';
