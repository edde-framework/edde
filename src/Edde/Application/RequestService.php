<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Edde;
	use Edde\Runtime\RuntimeException;
	use Edde\Service\Runtime\Runtime;
	use Edde\Service\Utils\StringUtils;

	class RequestService extends Edde implements IRequestService {
		use StringUtils;
		use Runtime;
		const PREG_CONTROLLER = '~^/?(?<class>[.a-z0-9-]+)/(?<method>[a-z0-9_-]+)$~';
		const PREG_REST = '~^/?rest/(?<class>[.a-z0-9-]+)$~';
		/** @var IRequest */
		protected $request;

		/** @inheritdoc */
		public function createRequest(): IRequest {
			return $this->request ?: $this->request = ($this->runtime->isConsoleMode() ? $this->createCliRequest() : $this->createHttpRequest());
		}

		/**
		 * @throws ApplicationException
		 */
		protected function createHttpRequest(): IRequest {
			if ($match = $this->stringUtils->match($path = ($requestUrl = $this->requestService->getUrl())->getPath(false), self::PREG_REST, true, true)) {
				return $this->factory($match['class'], strtolower($this->requestService->getMethod()), 'Rest', $requestUrl->getParams());
			} else if ($match = $this->stringUtils->match($path, self::PREG_CONTROLLER, true, true)) {
				return $this->factory($match['class'], $match['method'], 'Http', $requestUrl->getParams());
			}
			throw new ApplicationException('Cannot handle current HTTP request.');
		}

		/**
		 * @return IRequest
		 *
		 * @throws ApplicationException
		 * @throws RuntimeException
		 */
		protected function createCliRequest(): IRequest {
			$parameters = $this->runtime->getArguments();
			/**
			 * first parameter must be plain string in the same format, like in URL (for example foo.bar-service/do-this)
			 */
			if (isset($parameters[1]) === false || is_string($parameters[1]) === false) {
				throw new ApplicationException('First argument must be plain (just string)!');
			}
			if ($match = $this->stringUtils->match($parameters[1], self::PREG_CONTROLLER, true, true)) {
				return $this->factory($match['class'], $match['method'], 'Cli', array_slice($parameters, 2));
			}
			throw new ApplicationException('Cannot handle current Cli request.');
		}

		/**
		 * @param string $class
		 * @param string $method
		 * @param string $type
		 * @param array  $params
		 *
		 * @return IRequest
		 */
		protected function factory(string $class, string $method, string $type, array $params): IRequest {
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
			return new Request(
				implode('\\', $class),
				'action' . $this->stringUtils->toCamelCase($method),
				$params
			);
		}
	}
