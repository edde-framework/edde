<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Router\IRequest;
	use Edde\Common\Object\Object;

	class Request extends Object implements IRequest {
		/**
		 * @var IElement
		 */
		protected $element;

		public function __construct(IElement $element) {
			$this->element = $element;
		}

		/**
		 * @inheritdoc
		 */
		public function getElement(): IElement {
			return $this->element;
		}
	}
