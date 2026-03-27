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

	public static function getCategories($data) {
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['section|text']);
		
		$response = new \StdClass();

		$section = '';
		if ($data->section == 'men') {
			$section = 'catalog';
			$sectionSize = 'razmery-dlia-muzhchin';
		}
		if ($data->section == 'women') {
			$section = 'women-catalog';
			$sectionSize = 'raziery-dlia-zhenshchin';
		}

		$pageSection = wire('pages')->get('template=products, name=' . $section);
		$allCategories = $pageSection->children('template=category');

		$categories = [];
		foreach ($allCategories as $category) {
			$categories[] = [
				'id'  => $category->id,
				'name'  => $category->name,
				'title'  => $category->title,
				'count' => $category->numChildren(),
			];
		}

		$pageSizes = wire('pages')->get('template=sizes');
		$pageSizesSection = $pageSizes->get('name=' . $sectionSize);
		$allSizesSection = $pageSizesSection->children();

		$sizes = [];
		foreach ($allSizesSection as $size) {
			$sizes[] = [
				'id'  => $size->id,
				'name'  => $size->name,
				'title'  => $size->title,
				'russianSize' => $size->russian_size,
			];
		}

		$response->section = $data->section;
		$response->categories = $categories;
		$response->sizes = $sizes;

		return $response;
	}




	
	public static function getProducts($data) {
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);
		
		$response = new \StdClass();

		$limit = 9;
		$p = 0;
		$current_page = 1;

		if (!empty($data->page)) {
			$current_page = (int) $data->page;
			if ($current_page < 1) {
				$current_page = 1;
			}
			$p = ($current_page - 1) * $limit;
		}

		$section = '';
		if (!empty($data->section) && $data->section === 'men') {
			$section = 'catalog';
		}
		if (!empty($data->section) && $data->section === 'women') {
			$section = 'women-catalog';
		}

		$size = null;
		if ($data->size != 'all') {
			$size = (int) $data->size;
		} else {
			$size = null;
		}

		$pageSection = wire('pages')->get('template=products, name=' . $section);
		$pageCategory = $pageSection->get('template=category, name=' . $data->category);

		$selector = 'template=product';
		if ($size) {
			$selector .= ', sizes.size=' . $size;
		}

		$allProducts = $pageCategory->children($selector);

		$filteredProducts = new \ProcessWire\PageArray();

		foreach ($allProducts as $product) {
			if ($size) {
				$hasValidSize = false;

				foreach ($product->sizes as $sizeItem) {
					$sizeId = (int) $sizeItem->size->id;
					$quantityRaw = $sizeItem->quantity;

					$hasQuantity = $quantityRaw !== null && trim((string) $quantityRaw) !== '' && (int) $quantityRaw > 0;

					if ($sizeId === $size && $hasQuantity) {
						$hasValidSize = true;
						break;
					}
				}

				if (!$hasValidSize) {
					continue;
				}
			}

			$filteredProducts->add($product);
		}

		$total = $filteredProducts->count();
		$pagedProducts = $filteredProducts->slice($p, $limit);

		$products = [];
		foreach ($pagedProducts as $product) {
			$images = $product->images instanceof \ProcessWire\Pageimages ? $product->images : new \ProcessWire\Pageimages($product);
			$img1 = $images->first();
			$img2 = $images->eq(1);

			$productFullPriceRaw = (string) $product->price;
			$productDiscountRaw  = (string) $product->discount;

			$productFullPrice = (float) str_replace([' ', ','], ['', '.'], $productFullPriceRaw);
			$productDiscount  = (float) str_replace(['%', ' ', ','], ['', '', '.'], $productDiscountRaw);

			$price = (int) ceil($productFullPrice - ($productFullPrice * $productDiscount / 100)); 
			
			$raw = (string) $product->timer_sale;
			$dt = \DateTime::createFromFormat('d.m.Y', $raw);
			if ($dt) {
				$dt->setTime(23, 59, 59);
			}
			$endDate = $dt ? $dt->format('Y-m-d\TH:i:s') : null;
			$badge = '';
			$badgeType = '';
			if ($product->badge) {
				$badge = $product->badge->title;
				$badgeType = $product->badge->name;
			}

			if ($product->new == 1) {
				$badge = 'НОВИНКА';
				$badgeType = 'new';
			}

			if ($productDiscount > 0) {
				$badge = 'РАСПРОДАЖА';
				$badgeType = 'sale';
			}

			$products[] = [
				'id' => $product->id,
				'name' => $product->name,
				'title' => $product->title,
				'image' => $img1 ? $img1->url : '',
				'hoverImage' => $img2 ? $img2->url : ($img1 ? $img1->url : ''),
				'price' => $price,
				'fullPrice' => $productFullPrice,
				'discount' => $productDiscount,
				'badge' => $badge,
				'badgeType' => $badgeType,
				'endDate' => $endDate,
			];
		}

		$response->section = $data->section;
		$response->category = $data->category;
		$response->page = $current_page;
		$response->totalPage = ceil($total / $limit);
		$response->selectedSize = $size;
		$response->products = $products;

		return $response;
	}





	public static function getProductName($data) {
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['name|text']);
		
		$response = new \StdClass();
		$product = wire('pages')->get('template=product, name=' .$data->name);

		if (!$product->id) {
			throw new \Exception('Product not found', 404);
		}

		// Получаем поле SeoMaestro
    	$seoField = $product->seo;

		$metaData = [
			'title' => (string) $seoField->meta->title,
			'description' => (string) $seoField->meta->description,
			'keywords' => (string) $seoField->meta->keywords,
			'canonicalUrl' => (string) $seoField->meta->canonicalUrl,
		];

		$ogData = [
			'title'       => (string) $seoField->og->title,
			'description' => (string) $seoField->og->description,
			'image'       => (string) $seoField->og->image,
			'imageAlt'    => (string) $seoField->og->imageAlt,
			'type'        => (string) $seoField->og->type,
			'locale'      => (string) $seoField->og->locale,
			'siteName'    => (string) $seoField->og->siteName,
		];

		$categoryPage = $product->parent;          						// Page категории
		$sectionPage  = $categoryPage->parent;     						// Page раздела (родитель категории)

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

		$aboutText = strip_tags((string) $product->about_product);
		$aboutText = html_entity_decode($aboutText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$aboutText = preg_replace("/\R/u", "\n", $aboutText); // нормализовать переводы строк
		$aboutText = trim($aboutText);

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
				'idSize'  => $size->id,
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
		$productComments = $product->comments->find("status=1");
		foreach ($productComments as $comment) {
			$totalComments ++;
			$comments[] = [
				'id'  => $comment->id,
				'author' => $comment->cite,
				'text' => $comment->text,
				'created' => $comment->created,
			];
		}

		$likeit = [];
		// $likeitProducts = $categoryPage->children('template=product, limit=4');
		$likeitProducts = $categoryPage->children('template=product')->getRandom(4);
		foreach ($likeitProducts as $likeitProduct) {
			$img1 = $likeitProduct->images->eq(0);
			$img2 = $likeitProduct->images->eq(1);

			$likeitFullPriceRaw = (string) $likeitProduct->price;
			$likeitDiscountRaw  = (string) $likeitProduct->discount;

			$likeitFullPrice = (float) str_replace([' ', ','], ['', '.'], $likeitFullPriceRaw);
			$likeitDiscount  = (float) str_replace(['%', ' ', ','], ['', '', '.'], $likeitDiscountRaw);

			$likeitprice = (int) ceil($likeitFullPrice - ($likeitFullPrice * $likeitDiscount / 100)); 
			
			$raw = (string) $likeitProduct->timer_sale;
			$dt = \DateTime::createFromFormat('d.m.Y', $raw);
			if ($dt) {
				$dt->setTime(23, 59, 59);
			}
			$endDate = $dt ? $dt->format('Y-m-d\TH:i:s') : null;
			$badge = '';
			$badgeType = '';
			if ($likeitProduct->badge) {
				$badge = $likeitProduct->badge->title;
				$badgeType = $likeitProduct->badge->name;
			}
			if ($likeitProduct->new == 1) {
				$badge = 'НОВИНКА';
				$badgeType = 'new';
			}
			if ($likeitDiscount > 0) {
				$badge = 'РАСПРОДАЖА';
				$badgeType = 'sale';
			}

			$likeit[] = [
				'id'  => $likeitProduct->id,
				'name'  => $likeitProduct->name,
				'title'  => $likeitProduct->title,
				'image'      => $img1 ? $img1->url : '',
    			'hoverImage' => $img2 ? $img2->url : ($img1 ? $img1->url : ''),
				'price'  => $likeitprice,
				'fullPrice'  => $likeitFullPrice,
				'discount'  => $likeitDiscount,
				'badge'  => $badge,
				'badgeType'  => $badgeType,
				'endDate'  => $endDate,
			];
		}

		$productSectionName ='';
		if ($sectionPage->name === 'catalog') {
			$productSectionName = 'men';
		}
		if ($sectionPage->name === 'women-catalog') {
			$productSectionName = 'women';
		}

		$response->id = $product->id;
		$response->name = $product->name;
		$response->productCategory = $categoryPage->title;
		$response->productCategoryName = $categoryPage->name;
		$response->productSection  = $sectionPage->title;
		$response->productSectionName  = $productSectionName;
		$response->title = $product->title;
		$response->price = $price;
		$response->fullPrice = $fullPrice;
		$response->discount = $discount;
		$response->images = $images;
		$response->video = $video;
		$response->description = $descriptionText;
		$response->aboutProduct = $aboutText;
		$response->sameModels = $sameModels;
		$response->sizes = $sizes;
		$response->delivery = $delivery;
		$response->sku = $product->ksu;
		$response->category = $categoryPage->title . ', ' . $sectionPage->title;
		$response->tag = $categoryPage->title;
		$response->about = '';
		$response->specifications = $specifications;
		$response->totalComments = $totalComments;
		$response->comments = $comments;
		$response->idCategory = $categoryPage->id;
		$response->likeit = $likeit;
		$response->metaData = $metaData;
		$response->ogData = $ogData;

		return $response;
	}
}
