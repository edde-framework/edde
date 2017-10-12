<?php
	namespace Edde\Ext\Router;

		use Edde\Api\Protocol\Inject\ProtocolService;
		use Edde\Api\Request\IRequest;
		use Edde\Api\Router\Exception\RouterException;
		use Edde\Api\Utils\Inject\CliUtils;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Request\Request;
		use Edde\Common\Router\AbstractRouter;

		class ProtocolServiceRouter extends AbstractRouter {
			const PREG_CONTROLLER = '~^/?(?<class>[.a-z0-9-]+)/(?<method>[a-z0-9-]+)$~';
			const PREG_REST = '~^/?rest/(?<class>[.a-z0-9-]+)$~';
			use ProtocolService;
			use CliUtils;
			use StringUtils;

			/**
			 * @inheritdoc
			 */
			public function canHandle() : bool {
				try {
					return $this->protocolService->canHandle($this->createRequest()->getElement());
				} catch (\Throwable $exception) {
					return false;
				}
			}

			/**
			 * @inheritdoc
			 */
			public function createRequest() : IRequest {
				return $this->isHttp() ? $this->createHttpRequest() : $this->createCliRequest();
			}

			/**
			 * @inheritdoc
			 */
			protected function createHttpRequest() : IRequest {
				return $this->createRouteRequest(($requestUrl = $this->httpService->getRequest()->getRequestUrl())->getPath(false), $requestUrl->getParameterList(), 'Http');
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
					throw new RouterException('First argument must be plain (just string)!');
				}
				return $this->createRouteRequest($parameterList[1], array_slice($parameterList, 2), 'Cli');
			}

			/**
			 * @param string $path
			 * @param array  $parameterList
			 * @param string $type
			 *
			 * @return IRequest
			 * @throws RouterException
			 */
			protected function createRouteRequest(string $path, array $parameterList, string $type) : IRequest {
				if ($this->isHttp() && ($match = $this->stringUtils->match($path, self::PREG_REST, true, true))) {
					$match['method'] = strtolower($this->httpService->getRequest()->getMethod());
					$type = 'Rest';
				} else if (($match = $this->stringUtils->match($path, self::PREG_CONTROLLER, true, true)) === null) {
					throw new RouterException(sprintf('Cannot handle current url path [%s].', $path));
				}
				/**
				 * assignment is intentional
				 */
				$class = explode('\\', str_replace([
					' ',
					'-',
				], [
					'\\',
					'',
				], $this->stringUtils->capitalize(str_replace('.', ' ', $match['class']))));
				array_splice($class, -1, 0, $type);
				$element = $this->createElement($path, $parameterList);
				$element->mergeMetaList([
					'::class'  => implode('\\', $class),
					'::method' => 'action' . $this->stringUtils->toCamelCase($match['method']),
				]);
				return new Request($element, $this->getTargetList());
			}
		}
