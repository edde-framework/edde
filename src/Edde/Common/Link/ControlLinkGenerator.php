<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Common\Url\Url;

	class ControlLinkGenerator extends AbstractLinkGenerator {
		use LazyRequestTrait;
		use LazyRequestUrlTrait;

		public function link($generate, ...$parameterList) {
			list($generate, $parameterList) = $this->list($generate, $parameterList);
			if (is_array($generate) === false || count($generate) !== 2) {
				return null;
			}
			list($control, $action) = $generate;
			if (class_exists($control = is_object($control) ? get_class($control) : $control) === false) {
				return null;
			}
			if (($match = $this->match($control, $action)) === null) {
				return null;
			}
			return Url::create()
				->setQuery(array_merge($this->request->getCurrent()[2], $parameterList, $match))
				->getAbsoluteUrl();
		}
	}
