<?php

namespace ProcessWire;

class Mainpage {
	public static function mainInfo($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);
		
		$response = new \StdClass();

        $mainPage = wire('pages')->get('template=home');

		//ОСНОВНЫЕ ДАННЫЕ
		$info = [];
		$info[] = [
			'address' => $mainPage->address,
			'main_phone' => $mainPage->main_phone,
			'mobile_phone' => $mainPage->mobile_phone,
			'whatsapp' => $mainPage->whatsapp,
			'email' => $mainPage->email,
		];
		//ОСНОВНЫЕ ДАННЫЕ
        
		$response->info = $info;

		return $response;
    }





    public static function mainPage($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);
		
		$response = new \StdClass();

        $mainPage = wire('pages')->get('template=home');

		//ДЛЯ СЕКЦИИ НАШИ НОВИНКИ
        $categoriesForNew = $mainPage->categories_for_new;
        $btnFiltersForNew = ["Все новинки"];
        $filteredProducts = new \ProcessWire\PageArray();
        foreach ($categoriesForNew as $cat) {
            $btnFiltersForNew[] = $cat->title;

            $likeitProducts = $cat->children('template=product, new=1')->getRandom(7);
            $filteredProducts->add($likeitProducts);
        }

        $productsForNew = [];
		foreach ($filteredProducts as $product) {
            $categoryPage = $product->parent;

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

			$productsForNew[] = [
				'id' => $product->id,
                'category' => $categoryPage->title,
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
		//ДЛЯ СЕКЦИИ НАШИ НОВИНКИ

		//ДЛЯ СЕКЦИИ НАШИ КОЛЛЕКЦИИ
        $catalog = wire('pages')->get('template=products, name=catalog');
        $allCategories = $catalog->children('template=category');
        $ourCollections = [];
        foreach ($allCategories as $category) {
            $images = $category->images instanceof \ProcessWire\Pageimages ? $category->images : new \ProcessWire\Pageimages($category);
			$img = $images->first();
            $link = 'catalog/men/' . $category->name . '/all/1';
            $ourCollections[] = [
                'id' => $category->id,
				'name' => $category->name,
				'title' => $category->title,
                'image' => $img ? (string) $img->url : '',
                'link' => $link,
            ];
        }
		//ДЛЯ СЕКЦИИ НАШИ КОЛЛЕКЦИИ

		//ДЛЯ СЕКЦИИ ЛУЧШЕЕ
		$filteredProducts = new \ProcessWire\PageArray();

		$badge = wire('pages')->get('template=badge, name=top');
		$productsTop = wire('pages')->find('template=product, badge=' . $badge)->getRandom(7);
		$filteredProducts->add($productsTop);

		if ($productsTop->count() > 0) {
			$btnFiltersForBest = ["Топ", "Распродажа", "Новинка"];
		} else {
			$btnFiltersForBest = ["Распродажа", "Новинка"];
		}

		$productsSale = wire('pages')->find('template=product, discount>0')->getRandom(7);
		$filteredProducts->add($productsSale);

		$productsNew = wire('pages')->find('template=product, new=1')->getRandom(7);
		$filteredProducts->add($productsNew);

		$productsForBest = [];
		foreach ($filteredProducts as $product) {
            $categoryPage = $product->parent;

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

			$productsForBest[] = [
				'id' => $product->id,
                'category' => $categoryPage->title,
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
		//ДЛЯ СЕКЦИИ ЛУЧШЕЕ

		//ДЛЯ СЕКЦИИ ОТЗЫВЫ
		$fieldName = 'comments';
		$limit = 7;
		$pagesWithComments = wire('pages')->find("$fieldName.count>0, include=all");
		$all = [];
		foreach ($pagesWithComments as $p) {
			foreach ($p->$fieldName as $c) {

				// только одобренные (если метод есть)
				if (method_exists($c, 'isApproved') && !$c->isApproved()) continue;

				// исключим спам
				if (defined('ProcessWire\\Comment::statusSpam') && ($c->status & \ProcessWire\Comment::statusSpam)) continue;

				$all[] = [
					'page_id'  => $p->id,
					'author'   => $c->cite,
					'text'     => $c->text,
					'created'  => $c->created,
				];
			}
		}
		shuffle($all);
		$commentsForMain = array_slice($all, 0, $limit);
		//ДЛЯ СЕКЦИИ ОТЗЫВЫ
        
        $response->idMianPage = $mainPage->id;
        $response->btnFiltersForNew = $btnFiltersForNew;
        $response->productsForNew = $productsForNew;
        $response->ourCollections = $ourCollections;
		$response->btnFiltersForBest = $btnFiltersForBest;
		$response->productsForBest = $productsForBest;
		$response->commentsForMain = $commentsForMain;

		return $response;
    }





	public static function getPage($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['page|text']);
		
		$response = new \StdClass();

        $page = wire('pages')->get('template=basic-page|doc, name=' . $data->page);
        
		$response->pageid = $page->id;
		$response->name = $page->name;
		$response->title = $page->title;
		$response->body = $page->body;

		return $response;
    }
}