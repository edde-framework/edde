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
				return $this->request ?: $this->request = ($this->isHttp() ? $this->createHttpRequest() : $this->createCliRequest());
			}

			/**
			 * @inheritdoc
			 */
			protected function createHttpRequest() : IRequest {
				if ($match = $this->stringUtils->match($path = ($requestUrl = ($request = $this->httpService->getRequest())->getRequestUrl())->getPath(false), self::PREG_REST, true, true)) {
					return $this->factory($match['class'], strtolower($request->getMethod()), 'Rest', $requestUrl->getParameterList());
				} else if ($match = $this->stringUtils->match($path, self::PREG_CONTROLLER, true, true)) {
					return $this->factory($match['class'], $match['method'], 'Http', $requestUrl->getParameterList());
				}
				throw new RouterException('Cannot handle current HTTP request.');
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
				if ($match = $this->stringUtils->match($parameterList[1], self::PREG_CONTROLLER, true, true)) {
					return $this->factory($match['class'], $match['method'], 'Cli', array_slice($parameterList, 2));
				}
				throw new RouterException('Cannot handle current Cli request.');
			}

			protected function factory(string $class, string $method, string $type, array $parameterList) : IRequest {
				$class = explode('\\', str_replace([
					' ',
					'-',
				], [
					'\\',
					'',
				], $this->stringUtils->capitalize(str_replace('.', ' ', $class))));
				array_splice($class, -1, 0, $type);
				$element = $this->createElement($class = implode('\\', $class), $parameterList);
				$element->mergeMetaList([
					'::class'  => $class,
					'::method' => 'action' . $this->stringUtils->toCamelCase($method),
				]);
				return new Request($element, $this->getTargetList());
			}
		}
