<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol\Request;

	use Edde\Api\Application\LazyContextTrait;
	use Edde\Api\Control\IControl;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Protocol\Request\AbstractRequestHandler;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Handles namespaced, dotted paths with slash action
	 */
	class ControlRequestHandler extends AbstractRequestHandler {
		use LazyContextTrait;
		static protected $preg = '~(?<class>[\\\a-zA-Z0-9-]+)::(?<action>[a-zA-Z0-9-]+)~';

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			if (parent::canHandle($element) === false || ($match = StringUtils::match((string)$element->getAttribute('request'), self::$preg)) === null) {
				return false;
			}
			$element->setMeta('::class', $class = str_replace([
				' ',
				'-',
			], [
				'\\',
				'',
			], StringUtils::capitalize(str_replace('.', ' ', $match['class']))));
			$element->setMeta('::method', $match['action']);
			return class_exists($class) && (new \ReflectionClass($class))->implementsInterface(IControl::class);
		}
	}
