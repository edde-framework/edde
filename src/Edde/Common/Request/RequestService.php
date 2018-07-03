<?php
	declare(strict_types=1);

	namespace Edde\Common\Request;

	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Request\IRequestHandler;
	use Edde\Api\Request\IRequestService;
	use Edde\Common\Protocol\Error;
	use Edde\Common\Request\Exception\MissingResponseException;

	class RequestService extends AbstractRequestHandler implements IRequestService {
		use Container;
		/**
		 * @var IRequestHandler[]
		 */
		protected $requestHandlerList = [];
		/**
		 * @var IElement[]
		 */
		protected $responseList = [];

		/**
		 * @inheritdoc
		 */
		public function registerRequestHandler(IRequestHandler $requestHandler): IRequestService {
			$this->requestHandlerList[] = $requestHandler;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getResponseList(): array {
			return $this->responseList;
		}

		/**
		 * @inheritdoc
		 */
		public function request(IElement $element): IElement {
			if (isset($this->responseList[$id = $element->getId()])) {
				return $this->responseList[$id];
			}
			return $this->execute($element);
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			foreach ($this->requestHandlerList as $requestHandler) {
				if ($requestHandler->setup() && $requestHandler->canHandle($element)) {
					return true;
				}
			}
			return false;
		}

		/**
		 * @inheritdoc
		 */
		public function onExecute(IElement $element) {
			foreach ($this->requestHandlerList as $requestHandler) {
				/** @var $response IElement */
				if ($requestHandler->setup() && $requestHandler->canHandle($element)) {
					if (($response = $requestHandler->execute($element)) !== null && $response instanceof IElement) {
						return $this->responseList[$element->getId()] = $response->setReference($element);
					}
					if ($element->isType('request')) {
						return (new Error(100, sprintf('Internal error; request [%s] got no answer (response).', $element->getAttribute('request'))))->setException(MissingResponseException::class)->setReference($element);
					}
					return null;
				}
			}
			return (new Error(100, sprintf('Unhandled request [%s].', $element->getAttribute('request'))))->setException(Exception\UnhandledRequestException::class)->setReference($element);
		}
	}
