<?php
	declare(strict_types=1);

	namespace Edde\Common\Reflection;

	use Closure;
	use Edde\Api\Reflection\ReflectionException;
	use Edde\Common\Object\Object;
	use ReflectionClass;
	use ReflectionFunction;
	use ReflectionMethod;

	/**
	 * Set of tools for simplier reflection manipulation.
	 */
	class ReflectionUtils extends Object {
		/**
		 * @var \ReflectionProperty[]|ReflectionClass[]|ReflectionMethod[][]
		 */
		static protected $cache;

		static public function getReflectionClass($class): ReflectionClass {
			if (isset(self::$cache[$cacheId = 'class/' . (is_object($class) ? get_class($class) : $class)]) === false) {
				self::$cache[$cacheId] = new ReflectionClass($class);
			}
			return self::$cache[$cacheId];
		}

		/**
		 * bypass property visibility and set a given value
		 *
		 * @param $object
		 * @param $property
		 * @param $value
		 *
		 * @throws ReflectionException
		 */
		static public function setProperty($object, string $property, $value) {
			try {
				if (isset(self::$cache[$cacheId = 'property/' . (is_object($object) ? get_class($object) : $object) . $property]) === false) {
					$reflectionClass = new ReflectionClass($object);
					self::$cache[$cacheId] = $reflectionProperty = $reflectionClass->getProperty($property);
					$reflectionProperty->setAccessible(true);
				}
				self::$cache[$cacheId]->setValue($object, $value);
			} catch (\ReflectionException $exception) {
				throw new ReflectionException(sprintf('Property [%s::$%s] does not exists.', get_class($object), $property));
			}
		}

		/**
		 * bypass visibility and reads the given property of the given object
		 *
		 * @param $object
		 * @param $property
		 *
		 * @return mixed
		 * @throws ReflectionException
		 */
		static public function getProperty($object, string $property) {
			try {
				if (isset(self::$cache[$cacheId = 'property/' . (is_object($object) ? get_class($object) : $object) . $property]) === false) {
					$reflectionClass = new ReflectionClass($object);
					self::$cache[$cacheId] = $reflectionProperty = $reflectionClass->getProperty($property);
					$reflectionProperty->setAccessible(true);
				}
				return self::$cache[$cacheId]->getValue($object);
			} catch (\ReflectionException $exception) {
				throw new ReflectionException(sprintf('Property [%s::$%s] does not exists.', get_class($object), $property));
			}
		}

		/**
		 * @param string|array|callable $callback
		 *
		 * @return ReflectionFunction|ReflectionMethod
		 *
		 * @throws ReflectionException
		 */
		static public function getMethodReflection($callback) {
			if (is_string($callback) && class_exists($callback)) {
				$reflectionClass = self::getReflectionClass($callback);
				if ($constructor = $reflectionClass->getConstructor()) {
					return $constructor;
				}
				return new ReflectionFunction(function () {
				});
			} else if ((is_string($callback) && strpos($callback, '::') === false) || $callback instanceof Closure) {
				return new ReflectionFunction($callback);
			} else if (is_array($callback) || (is_string($callback) && strpos($callback, '::') !== false)) {
				list($class, $method) = is_array($callback) ? $callback : explode('::', $callback);
				return new ReflectionMethod($class, $method);
			}
			throw new ReflectionException('Cannot get reflection method from the given input.');
		}

		/**
		 * @param callable|string $callback
		 *
		 * @return \ReflectionParameter[]
		 */
		static public function getParameterList($callback): array {
			$parameterList = [];
			$reflection = ReflectionUtils::getMethodReflection($callback);
			foreach ($reflection->getParameters() as $reflectionParameter) {
				$parameterList[$reflectionParameter->getName()] = $reflectionParameter;
			}
			return $parameterList;
		}

		/**
		 * @param mixed    $class
		 * @param int|null $filter
		 *
		 * @return ReflectionMethod[]
		 */
		static public function getMethodList($class, int $filter = null): array {
			if (isset(self::$cache[$cacheId = 'method-list/' . $filter . '/' . (is_object($class) ? get_class($class) : $class)]) === false) {
				$reflectionClass = self::getReflectionClass($class);
				self::$cache[$cacheId] = $filter ? $reflectionClass->getMethods($filter) : $reflectionClass->getMethods();
			}
			return self::$cache[$cacheId];
		}

		/**
		 * as usual, if there are two lambdas on the same line, this will fail
		 *
		 * @param callable $callable
		 *
		 * @return string
		 */
		static public function getMethodHash(callable $callable): string {
			$reflectionMethod = self::getMethodReflection($callable);
			return sha1($reflectionMethod->getStartLine() . $reflectionMethod->getEndLine() . $reflectionMethod->getName());
		}
	}
