<?php

namespace ProcessWire;

class Confirm {
	public static function confirmOrder($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);

		$response = new \StdClass();

        $response->orderId = $data->paymentMethod;

		return $response;
    }
}