<?php
	declare(strict_types = 1);

	namespace Edde\Common\Callback;

	use Closure;
	use Edde\Api\Callback\ICallback;
	use Edde\Api\Callback\IParameter;
	use Edde\Common\AbstractObject;
	use ReflectionClass;
	use ReflectionFunction;
	use ReflectionMethod;

	/**
	 * Useful set of methods around callable reflections.
	 */
	class CallbackUtils extends AbstractObject {
		/**
		 * @param callable|string $callback
		 *
		 * @return IParameter[]
		 */
		static public function getParameterList($callback): array {
			$reflection = self::getReflection($callback);
			$dependencyList = [];
			foreach ($reflection->getParameters() as $reflectionParameter) {
				$dependencyList[$reflectionParameter->getName()] = new Parameter($reflectionParameter->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() : null, $reflectionParameter->isOptional());
			}
			return $dependencyList;
		}

		/**
		 * @param string|array|callable $callback
		 *
		 * @return ReflectionFunction|ReflectionMethod
		 */
		static public function getReflection($callback) {
			if (is_string($callback) && class_exists($callback)) {
				$reflectionClass = new ReflectionClass($callback);
				$callback = $reflectionClass->hasMethod('__construct') ? [
					$callback,
					'__construct',
				] : function () use ($reflectionClass) {
					return $reflectionClass->newInstance();
				};
			} else if ($callback instanceof Closure) {
				$reflectionFunction = new ReflectionFunction($callback);
				if (substr($reflectionFunction->getName(), -1) === '}') {
					$vars = $reflectionFunction->getStaticVariables();
					$callback = $vars['_callable_'] ?? $callback;
				} else if ($obj = $reflectionFunction->getClosureThis()) {
					$callback = [
						$obj,
						$reflectionFunction->getName(),
					];
				} else if ($class = $reflectionFunction->getClosureScopeClass()) {
					$callback = [
						$class->getName(),
						$reflectionFunction->getName(),
					];
				} else {
					$callback = $reflectionFunction->getName();
				}
			} else if ($callback instanceof ICallback) {
				$callback = $callback->getCallback();
			}
			$class = ReflectionMethod::class;
			if (is_string($callback) && strpos($callback, '::')) {
				return new $class($callback);
			} else if (is_array($callback)) {
				return new $class($callback[0], $callback[1]);
			} else if (is_object($callback) && ($callback instanceof Closure) === false) {
				return new $class($callback, '__invoke');
			}
			return new ReflectionFunction($callback);
		}

		/**
		 * safely invoke given callback
		 *
		 * @param callable $function
		 * @param array $args
		 * @param callable $callback
		 *
		 * @return mixed
		 * @throws \Exception
		 */
		static public function invoke(callable $function, array $args, callable $callback) {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$errorHandler = set_error_handler(function ($severity, $message, $file) use ($callback, &$errorHandler) {
				if ($file === __FILE__ && $callback($message, $severity) !== false) {
					return null;
				} else if ($errorHandler) {
					return call_user_func_array($errorHandler, func_get_args());
				}
				return false;
			});
			try {
				$result = call_user_func_array($function, $args);
				restore_error_handler();
				return $result;
			} catch (\Exception $e) {
				restore_error_handler();
				throw $e;
			}
		}
	}
