<?php
	namespace Edde\Ext\Router;

		use Edde\Api\Element\IElement;
		use Edde\Api\Protocol\Inject\ProtocolService;
		use Edde\Api\Request\IRequest;
		use Edde\Common\Router\AbstractRouter;

		class ProtocolServiceRouter extends AbstractRouter {
			use ProtocolService;
			/**
			 * @var IElement
			 */
			protected $element;

			/**
			 * @inheritdoc
			 */
			public function canHandle(): bool {
				return $this->protocolService->canHandle($this->element);
			}

			/**
			 * @inheritdoc
			 */
			public function createRequest(): IRequest {
			}

			protected function createElement(): IElement {
				if ($this->element) {
					return $this->element;
				}
			}
		}
