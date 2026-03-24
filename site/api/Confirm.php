<?php

namespace ProcessWire;

class Confirm {
	public static function confirmOrder($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);
		
		$response = new \StdClass();

        $page = $data->asd;
        
		$response->page = $page;

		return $response;
    }
}