<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Router\IRequest;

	/**
	 * Static router all the times returns same request.
	 */
	class StaticRouter extends AbstractRouter {
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
		public function canHandle(): bool {
			return true;
		}

		/**
		 * @inheritdoc
		 */
		public function createRequest(): IRequest {
			return new Request($this->element);
		}
	}
