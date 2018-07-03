<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Http\Inject\HttpService;
	use Edde\Api\Log\Inject\LogService;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\Inject\ProtocolService;
	use Edde\Api\Router\Exception\RouterException;
	use Edde\Api\Router\IRequest;
	use Edde\Api\Runtime\Inject\Runtime;
	use Edde\Common\Cli\CliUtils;
	use Edde\Common\Request\Message;

	/**
	 * Router to check if the protocol is able to handle incoming request.
	 */
	class ProtocolRouter extends AbstractRouter {
		use HttpService;
		use ProtocolService;
		use LogService;
		use Runtime;
		/**
		 * @var IElement
		 */
		protected $element;

		public function canHandle(): bool {
			try {
				return $this->protocolService->canHandle($this->runtime->isConsoleMode() ? $this->createCliElement() : $this->createHttpElement());
			} catch (\Exception $e) {
				$this->logService->exception($e, ['edde']);
				return false;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function createRequest(): IRequest {
			return new Request($this->element);
		}

		protected function createHttpElement(): IElement {
			$requestUrl = $this->httpService->createRequest()
				->getRequestUrl();
			return $this->createMessage($requestUrl->getPath(false), $requestUrl->getParameterList());
		}

		/**
		 * @return IElement
		 * @throws RouterException
		 */
		protected function createCliElement(): IElement {
			if (isset($GLOBALS['argv']) === false) {
				throw new RouterException("There is no \$GLOBALS['argv']!");
			}
			$argumentList = CliUtils::getArgumentList($GLOBALS['argv']);
			/**
			 * first parameter must be plain string in the same format, like in URL (for example foo.bar-service/do-this)
			 */
			if (isset($argumentList[1]) === false) {
				throw new RouterException("First argument must be plain (just string)!");
			}
			return $this->createMessage($argumentList[1], array_slice($argumentList, 2));
		}

		protected function createMessage(string $request, array $parameterList): IElement {
			$this->element = $message = new Message($request);
			$message->appendAttributeList($parameterList);
			return $message;
		}
	}
