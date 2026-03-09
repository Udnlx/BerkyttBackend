<?php

namespace ProcessWire;


/**
 * Установка переменных для возврата в случае ошибки.
 *
 * @param mixed $error Текст ошибки
 * @return array
 */
function setError($error, $out) {
	$out['status'] = 'ERROR';
	$out['error'] = $error;
	return $out;
}

$result = ['status' => 'OK'];
$method = $input->requestMethod();
//$result['data'] = [];

if ($method) {
	if ($page->name == 'api') {
		if ($input->urlSegment(1)) {
			$file = './api/' . $method . '/' . $input->urlSegment(1) . '.php';
			if (file_exists($file)) {
				include($file);
			} else {
				$result = setError("Handler for the query {" . $input->urlSegment(1) . "} is not defined", $result);
			}
		} else {
			$result['data'] = [
				'version' => '1.0.0'
			];
		}
	} else {
		$result = setError("EndPoint ({$page->name}) is not defined", $result);
	}
} else {
	$result = setError("Method is not defined", $result);
}

if (is_array($result)) {
	echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
	echo $result;
}
