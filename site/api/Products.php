<?php

namespace ProcessWire;

class Products {
	// public static function getProductID($data) {
	// 	$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);
		
	// 	$response = new \StdClass();
	// 	$product = wire('pages')->get('template=product, id=' .$data->id);

	// 	if (!$product->id) {
	// 		throw new \Exception('Product not found', 404);
	// 	}

	// 	$response->id = $product->id;
	// 	$response->name = $product->name;

	// 	return $response;
	// }

	public static function getProductName($data) {
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['name|text']);
		
		$response = new \StdClass();
		$product = wire('pages')->get('template=product, name=' .$data->name);

		if (!$product->id) {
			throw new \Exception('Product not found', 404);
		}

		$categoryPage = $product->parent;          						// Page категории
		$sectionPage  = $categoryPage->parent;     						// Page раздела (родитель категории)
		$fullPrice = $product->price;    		   						// Полная цена товара
		$discount = $product->discount;    		   						// Скидка товара

		$fullPriceRaw = (string) $product->price;
		$discountRaw  = (string) $product->discount;

		$fullPrice = (float) str_replace([' ', ','], ['', '.'], $fullPriceRaw);
		$discount  = (float) str_replace(['%', ' ', ','], ['', '', '.'], $discountRaw);

		$price = (int) ceil($fullPrice - ($fullPrice * $discount / 100));   	// Цена

		$images = [];
		foreach ($product->images as $image) {
			$images[] = $image->url;
		}

		$video = [];
		if ($product->video) {
			$video[] = $product->video->url;
		}

		$descriptionText = strip_tags((string) $product->body);
		$descriptionText = html_entity_decode($descriptionText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$descriptionText = preg_replace("/\R/u", "\n", $descriptionText); // нормализовать переводы строк
		$descriptionText = trim($descriptionText);

		$sameModels = [];
		if ($product->same_models) {
			foreach ($product->same_models as $model) {
				$sameModels[] = [
					'title'  => $model->title,
					'name'  => $model->name,
					'color'  => $model->color->title,
				];
			}
		}

		$sizes = [];
		foreach ($product->sizes as $size) {
			$sizes[] = [
				'scancode'  => $size->scancode,
				'storage'  => $size->storage,
				'russianSize'  => $size->size->russian_size,
				'size'  => $size->size->title,
				'quantity'  => $size->quantity,
				'price'  => $size->price,
			];
		}

		$months = [
			1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
			5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
			9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
		];
		$dateFrom = new \DateTime();
		$dateTo   = new \DateTime();
		$dateFrom->modify('+5 days');
		$dateTo->modify('+7 days');
		$delivery =
		(int)$dateFrom->format('j') . ' ' . $months[(int)$dateFrom->format('n')]
		. ' - ' .
		(int)$dateTo->format('j') . ' ' . $months[(int)$dateTo->format('n')];

		$specifications = [];
		if ($product->season) {
			$specifications[] = [
				'name'  => 'Сезон',
				'value' => $product->season->title,
			];
		}
		if ($product->gender) {
			$specifications[] = [
				'name'  => 'Пол',
				'value' => $product->gender->title,
			];
		}
		if ($product->age) {
			$specifications[] = [
				'name'  => 'Возраст',
				'value' => $product->age->title,
			];
		}
		if ($product->color) {
			$specifications[] = [
				'name'  => 'Цвет',
				'value' => $product->color->title,
			];
		}
		if ($product->pattern) {
			$specifications[] = [
				'name'  => 'Узор',
				'value' => $product->pattern->title,
			];
		}
		if ($product->length) {
			$specifications[] = [
				'name'  => 'Длина изделия',
				'value' => $product->length,
			];
		}
		if ($product->main_material) {
			foreach ($product->main_material as $item) {
				$specifications[] = [
					'name'  => 'Ткань - ' . $item->fabric->title,
					'value' => $item->percentage,
				];
			}
		}
		if ($product->back_material) {
			foreach ($product->back_material as $item) {
				$specifications[] = [
					'name'  => 'Ткань подкладки - ' . $item->fabric->title,
					'value' => $item->percentage,
				];
			}
		}

		$comments = [];
		$totalComments = 0;
		$productComments = $product->comments->find("status>=0");
		foreach ($productComments as $comment) {
			$totalComments ++;
			$comments[] = [
				'id'  => $comment->id,
				'author' => $comment->cite,
				'email' => $comment->email,
				'text' => $comment->text,
				'created' => $comment->created,
			];
		}


		$response->id = $product->id;
		$response->name = $product->name;
		$response->productCategory = $categoryPage->title;
		$response->productSection  = $sectionPage->title;
		$response->title = $product->title;
		$response->price = $price;
		$response->fullPrice = $fullPrice;
		$response->discount = $discount;
		$response->images = $images;
		$response->video = $video;
		$response->description = $descriptionText;
		$response->sameModels = $sameModels;
		$response->sizes = $sizes;
		$response->delivery = $delivery;
		$response->sku = $product->ksu;
		$response->category = $categoryPage->title . ', ' . $sectionPage->title;
		$response->tag = $categoryPage->title;
		$response->about = '';
		$response->specifications = $specifications;
		$response->comments = $comments;
		$response->totalComments = $totalComments;

		return $response;
	}
}
