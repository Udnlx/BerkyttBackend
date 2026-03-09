<?php

namespace ProcessWire;

if ($input->id) {
	$p = $pages->get('template=size, id=' . $input->id);
	if ($p->id) {
		$text = '<div class="uk-text-small uk-text-uppercase">Размер: ' . $p->title . '</div>';
		$text .= '<hr class="uk-margin-small">';
		$text .= $p->body;
		$data['title'] = $p->title;
		$data['description'] = $text;
		$result['data'] = $data;
	} else {
		$result = setError('Такого размера не существует.', $result);
	}
} else {
	$result = setError('Неверно задан размер.', $result);
}
