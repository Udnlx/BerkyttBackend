<?php

namespace ProcessWire;

$sdek_param = $pages->get('template=sdek');

if ($input->codecity) {
    $qnt = $input->get('qnt');
    $codecity = $input->get('codecity');
    $cart_weight = $qnt * ($sdek_param->weight*1000);

    //Начальные данные
    $client_id = $sdek_param->client_id;
    $client_secret = $sdek_param->client_secret;

    $code_from_location = 468;
    $code_to_location = $codecity;
    $weight = $cart_weight;
    
    //КОД ПОЛУЧЕНИЯ ЦЕНЫ ЧЕРЕЗ API СДЭКа
    // Авторизация
    $array = array();
    $array['grant_type']    = 'client_credentials';
    $array['client_id']     = $client_id; 
    $array['client_secret'] = $client_secret; 
    
    $ch = curl_init('https://api.cdek.ru/v2/oauth/token');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array, '', '&')); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $html = curl_exec($ch);
    curl_close($ch);
    
    $res = json_decode($html, true);
    $access_token = $res['access_token'];
    
    // Стоимость доставки
    $array = array();
    $array['tariff_code'] = 136;		
            
    $array['from_location'] = array(
        'code' => $code_from_location
    );	
        
    $array['to_location'] = array(
        'code' => $code_to_location
    );
    
    $array['packages'][] = array(
        'height' => $sdek_param->height,
        'length' => $sdek_param->length,
        'weight'  => $weight,
        'width' => $sdek_param->width,
    );
    
    $array = json_encode($array, JSON_PRETTY_PRINT);
    $ch = curl_init('https://api.cdek.ru/v2/calculator/tariff');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $array); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $html = curl_exec($ch);
    curl_close($ch);
        
    $res = json_decode($html, true);	
    $delivery_sum = $res['total_sum'];
    $price = $delivery_sum;

    $result['qnt'] = $qnt;
    $result['codecity'] = $codecity;
    $result['cart_weight'] = $cart_weight;
    $result['price'] = $price;
    $result['price_courier'] = $price + 250;
} else {
	$result = setError('Неверно задан параметр.', $result);
}
