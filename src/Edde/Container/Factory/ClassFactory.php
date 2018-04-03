<?php
	declare(strict_types=1);
	namespace Edde\Container\Factory;

	use Edde\Container\ContainerException;
	use Edde\Container\IAutowire;
	use Edde\Container\IContainer;
	use Edde\Container\IReflection;
	use Edde\Container\Parameter;
	use Edde\Container\Reflection;
	use ReflectionClass;
	use ReflectionFunction;
	use ReflectionMethod;

	class ClassFactory extends AbstractFactory {
		/** @var IReflection[] */
		static protected $reflectionCache = [];

		/** @inheritdoc */
		public function canHandle(IContainer $container, string $dependency): bool {
			return class_exists($dependency) && interface_exists($dependency) === false;
		}

		/** @inheritdoc */
		public function getReflection(IContainer $container, string $dependency): IReflection {
			if (isset(self::$reflectionCache[$dependency])) {
				return self::$reflectionCache[$dependency];
			}
			$lazies = [];
			$configurators = [];
			$reflectionClass = new ReflectionClass($dependency);
			foreach ($reflectionClass->getMethods() as $reflectionMethod) {
				$parameterReflectionClass = $reflectionMethod->getDeclaringClass();
				if ($parameterReflectionClass->implementsInterface(IAutowire::class)) {
					$lazies = array_merge($lazies, $this->getParams($parameterReflectionClass, $reflectionMethod, 'lazy'));
				}
			}
			$params = [];
			$constructor = $reflectionClass->getConstructor() ?: new ReflectionFunction(function () {
			});
			foreach ($constructor->getParameters() as $reflectionParameter) {
				if (($parameterReflectionClass = $reflectionParameter->getClass()) === null) {
					break;
				}
				$params[] = new Parameter($reflectionParameter->getName(), $reflectionParameter->isOptional(), $parameterReflectionClass->getName());
			}
			if ($dependency !== null) {
				$configurators = array_reverse(array_merge([$dependency], (new ReflectionClass($dependency))->getInterfaceNames()));
			}
			return self::$reflectionCache[$dependency] = new Reflection($params, $lazies, $configurators);
		}

		/** @inheritdoc */
		public function factory(IContainer $container, array $params, IReflection $dependency, string $name = null) {
			$params = $this->parameters($container, $params, $dependency);
			if (empty($params)) {
				return new $name();
			}
			return new $name(...$params);
		}

		/**
		 * @param ReflectionClass  $reflectionClass
		 * @param ReflectionMethod $reflectionMethod
		 * @param string           $method
		 *
		 * @return array
		 *
		 * @throws ContainerException
		 */
		protected function getParams(ReflectionClass $reflectionClass, ReflectionMethod $reflectionMethod, string $method) {
			$params = [];
			if (strlen($name = $reflectionMethod->getName()) > strlen($method) && strpos($name, $method, 0) === 0) {
				if ($reflectionMethod->isPublic() === false) {
					throw new ContainerException(sprintf('Method [%s::%s()] must be public.', $reflectionClass->getName(), $reflectionMethod->getName()));
				}
				foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
					if ($reflectionClass->hasProperty($name = $reflectionParameter->getName()) === false) {
						throw new ContainerException(sprintf('Class [%s] must have property [$%s] of the same name as parameter in method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
					} else if (($class = $reflectionParameter->getClass()) === null) {
						throw new ContainerException(sprintf('Class [%s] must have property [$%s] with class type hint in method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
					}
					$reflectionProperty = $reflectionClass->getProperty($name);
					$reflectionProperty->setAccessible(true);
					$params[] = new Parameter($reflectionProperty->getName(), false, $class->getName());
				}
			}
			return $params;
		}
	}
