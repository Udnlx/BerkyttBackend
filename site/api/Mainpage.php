<?php

namespace ProcessWire;

class Mainpage {
    public static function mainPage($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, []);
		
		$response = new \StdClass();

        $mainPage = wire('pages')->get('template=home');

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
        
        $response->idMianPage = $mainPage->id;
        $response->btnFiltersForNew = $btnFiltersForNew;
        $response->productsForNew = $productsForNew;
        $response->ourCollections = $ourCollections;

		return $response;
    }
}