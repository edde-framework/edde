<?php
	declare(strict_types=1);
	namespace Edde\Ext\Router;

		use Edde\Api\Element\IElement;
		use Edde\Api\Request\IRequest;
		use Edde\Common\Request\Request;
		use Edde\Common\Router\AbstractRouter;

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
				return new Request($this->element, $this->getTargetList());
			}
		}
