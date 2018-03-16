<?php
	declare(strict_types=1);
	namespace Edde\Ext\Router;

	use Edde\Api\Bus\Request\IRequest;
	use Edde\Api\Http\Exception\NoHttpException;
	use Edde\Api\Router\Exception\RouterException;
	use Edde\Api\Runtime\Exception\MissingArgvException;
	use Edde\Api\Utils\Inject\StringUtils;
	use Edde\Common\Bus\Request\Request;
	use Edde\Common\Router\AbstractRouter;
	use Edde\Inject\Container\Container;
	use Edde\Inject\Crypt\RandomService;

	/**
	 * Maybe not the best name: this router provides application request made from
	 * CLI and from HTTP request.
	 */
	class RequestRouter extends AbstractRouter {
		use Container;
		use StringUtils;
		use RandomService;
		const PREG_CONTROLLER = '~^/?(?<class>[.a-z0-9-]+)/(?<method>[a-z0-9_-]+)$~';
		const PREG_REST = '~^/?rest/(?<class>[.a-z0-9-]+)$~';

		/** @inheritdoc */
		public function canHandle(): bool {
			try {
				return $this->container->canHandle($this->createRequest()->getService());
			} catch (\Throwable $exception) {
				return false;
			}
		}

		/**
		 * @inheritdoc
		 *
		 * @throws NoHttpException
		 * @throws RouterException
		 * @throws MissingArgvException
		 */
		public function createRequest(): IRequest {
			return $this->request ?: $this->request = ($this->isHttp() ? $this->createHttpRequest() : $this->createCliRequest());
		}

		/**
		 * @throws NoHttpException
		 * @throws RouterException
		 */
		protected function createHttpRequest(): IRequest {
			if ($match = $this->stringUtils->match($path = ($requestUrl = $this->requestService->getUrl())->getPath(false), self::PREG_REST, true, true)) {
				return $this->factory($match['class'], strtolower($this->requestService->getMethod()), 'Rest', $requestUrl->getParameterList());
			} else if ($match = $this->stringUtils->match($path, self::PREG_CONTROLLER, true, true)) {
				return $this->factory($match['class'], $match['method'], 'Http', $requestUrl->getParameterList());
			}
			throw new RouterException('Cannot handle current HTTP request.');
		}

		/**
		 * @throws MissingArgvException
		 * @throws NoHttpException
		 * @throws RouterException
		 */
		protected function createCliRequest(): IRequest {
			$parameters = $this->runtime->getArguments();
			/**
			 * first parameter must be plain string in the same format, like in URL (for example foo.bar-service/do-this)
			 */
			if (isset($parameters[1]) === false || is_string($parameters[1]) === false) {
				throw new RouterException('First argument must be plain (just string)!');
			}
			if ($match = $this->stringUtils->match($parameters[1], self::PREG_CONTROLLER, true, true)) {
				return $this->factory($match['class'], $match['method'], 'Cli', array_slice($parameters, 2));
			}
			throw new RouterException('Cannot handle current Cli request.');
		}

		/**
		 * @param string $class
		 * @param string $method
		 * @param string $type
		 * @param array  $parameters
		 *
		 * @return IRequest
		 *
		 * @throws NoHttpException
		 */
		protected function factory(string $class, string $method, string $type, array $parameters): IRequest {
			$class = explode('\\', str_replace([
				' ',
				'-',
			], [
				'\\',
				'',
			], $this->stringUtils->capitalize(str_replace('.', ' ', $class))));
			array_splice($class, 0, 0, $type);
			/**
			 * this is synthetic restriction to keep requests just for controller classes
			 */
			$class[$index] = $class[$index = (count($class) - 1)] . 'Controller';
			$request = new Request(
				implode('\\', $class),
				'action' . $this->stringUtils->toCamelCase($method),
				$this->randomService->uuid(),
				$parameters
			);
			$request->setAttribute('targets', $this->getTargets());
			return $request;
		}
	}
