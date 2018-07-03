<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Common\Strings\StringUtils;

	class SimpleHttpRouter extends HttpRouter {
		/**
		 * @var string[]
		 */
		protected $namespaceList;

		/**
		 * One day a blond walks into a doctors office with both of her ears burnt.
		 * The doctor askes her what had happened.
		 * She says, "well... when I was ironing my work suit the phone rang and I mistakanly picked up the iron instead of the phone."
		 * "Well that explains one ear, but what about the other."
		 * "The bastard called again"
		 *
		 * @param array $namespaceList
		 */
		public function __construct(array $namespaceList) {
			$this->namespaceList = $namespaceList;
		}

		public function createRequest() {
			$this->use();
			if ($this->runtime->isConsoleMode()) {
				return null;
			}
			$pathList = $this->requestUrl->getPathList();
			if (empty($pathList)) {
				return null;
			}
			$path = explode('.', array_shift($pathList));
			if (count($path) !== 2) {
				return null;
			}
			list($control, $action) = $path;
			$name = StringUtils::toCamelCase($control);
			$parameterList = $this->requestUrl->getQuery();
			foreach ($this->namespaceList as $namespace) {
				$classList = [
					sprintf('%s\\%s\\%sView', $namespace, $name, $name),
					sprintf('%s\\%s\\%sControl', $namespace, $name, $name),
				];
				foreach ($classList as $class) {
					if (class_exists($class)) {
						$this->requestUrl->setPath('');
						$parameterList['action'] = $class . '.' . $action;
						break 2;
					}
				}
			}
			if (isset($parameterList['action']) === false && isset($parameterList['handle']) === false) {
				return null;
			}
			$this->requestUrl->setQuery($parameterList);
			return parent::createRequest();
		}
	}
