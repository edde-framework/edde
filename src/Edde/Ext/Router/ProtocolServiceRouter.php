<?php
	namespace Edde\Ext\Router;

		use Edde\Api\Protocol\Inject\ProtocolService;
		use Edde\Api\Request\IRequest;
		use Edde\Api\Router\Exception\RouterException;
		use Edde\Api\Utils\Inject\CliUtils;
		use Edde\Common\Request\Request;
		use Edde\Common\Router\AbstractRouter;

		class ProtocolServiceRouter extends AbstractRouter {
			use ProtocolService;
			use CliUtils;

			/**
			 * @inheritdoc
			 */
			public function canHandle() : bool {
				return $this->protocolService->canHandle($this->createRequest()->getElement());
			}

			/**
			 * @inheritdoc
			 */
			protected function createHttpRequest() : IRequest {
				$requestUrl = $this->httpService->getRequest()->getRequestUrl();
				return new Request($this->createElement($requestUrl->getPath(false), $requestUrl->getParameterList()), $this->getTargetList());
			}

			/**
			 * @inheritdoc
			 */
			protected function createCliRequest() : IRequest {
				/**
				 * it's better to not relay on the global argc/v; they can be
				 * safely accessed from globals array
				 */
				if (isset($GLOBALS['argv']) === false) {
					throw new RouterException("There is no \$GLOBALS['argv']!");
				}
				$parameterList = $this->cliUtils->getArgumentList($GLOBALS['argv']);
				/**
				 * first parameter must be plain string in the same format, like in URL (for example foo.bar-service/do-this)
				 */
				if (isset($parameterList[1]) === false || is_string($parameterList[1]) === false) {
					throw new RouterException("First argument must be plain (just string)!");
				}
				return new Request($this->createElement($parameterList[1], array_slice($parameterList, 2)));
			}
		}
