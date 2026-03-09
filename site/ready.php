<?php

namespace ProcessWire;

$wire->addHookAfter(
	"Page(template=product)::changed(same_models)",
	function (HookEvent $event) {
		$page = $event->object;
		$models = $event->arguments(2);
		$event->addHookAfter(
			"Pages::saved($page)",
			function ($event) use ($page, $models) {
				foreach ($models as $model) {
					if (!$model->same_models->has($page)) {
						$event->message("В товар - $model->title добавлено сопастовление с текущим товаром.");
						$model->of(false);
						$model->same_models->add($models->find('id!=' . $model->id));
						$model->same_models->add($page);
						$model->save();
					}
				}
				$event->removeHook(null);
			}
		);
	}
);
