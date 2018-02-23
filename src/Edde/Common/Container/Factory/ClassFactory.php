<?php
	declare(strict_types=1);
	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IAutowire;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IReflection;
	use Edde\Common\Container\Factory\Exception\MethodVisibilityException;
	use Edde\Common\Container\Factory\Exception\MissingClassException;
	use Edde\Common\Container\Factory\Exception\PropertyVisibilityException;
	use Edde\Common\Container\Parameter;
	use Edde\Common\Container\Reflection;
	use ReflectionClass;
	use ReflectionFunction;
	use ReflectionMethod;

	class ClassFactory extends AbstractFactory {
		/**
		 * @var IReflection[]
		 */
		static protected $reflectionCache = [];

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			return class_exists($dependency) && interface_exists($dependency) === false;
		}

		/**
		 * @inheritdoc
		 */
		public function getReflection(IContainer $container, string $dependency): IReflection {
			if (isset(self::$reflectionCache[$dependency])) {
				return self::$reflectionCache[$dependency];
			}
			$injectList = [];
			$lazyList = [];
			$configuratorList = [];
			$reflectionClass = new ReflectionClass($dependency);
			foreach ($reflectionClass->getMethods() as $reflectionMethod) {
				$injectList = array_merge($injectList, $this->getParameterList($parameterReflectionClass = $reflectionMethod->getDeclaringClass(), $reflectionMethod, 'inject'));
				if ($parameterReflectionClass->implementsInterface(IAutowire::class)) {
					$lazyList = array_merge($lazyList, $this->getParameterList($parameterReflectionClass, $reflectionMethod, 'lazy'));
				}
			}
			$parameterList = [];
			$constructor = $reflectionClass->getConstructor() ?: new ReflectionFunction(function () {
			});
			foreach ($constructor->getParameters() as $reflectionParameter) {
				if (($parameterReflectionClass = $reflectionParameter->getClass()) === null) {
					break;
				}
				$parameterList[] = new Parameter($reflectionParameter->getName(), $reflectionParameter->isOptional(), $parameterReflectionClass->getName());
			}
			if ($dependency !== null) {
				$configuratorList = array_reverse(array_merge([$dependency], (new ReflectionClass($dependency))->getInterfaceNames()));
			}
			return self::$reflectionCache[$dependency] = new Reflection($parameterList, $injectList, $lazyList, $configuratorList);
		}

		/**
		 * @inheritdoc
		 */
		public function factory(IContainer $container, array $parameterList, IReflection $dependency, string $name = null) {
			$parameterList = $this->parameters($container, $parameterList, $dependency);
			if (empty($parameterList)) {
				return new $name();
			}
			return new $name(...$parameterList);
		}

		/**
		 * @param ReflectionClass  $reflectionClass
		 * @param ReflectionMethod $reflectionMethod
		 * @param string           $method
		 *
		 * @return array
		 * @throws MethodVisibilityException
		 * @throws PropertyVisibilityException
		 * @throws MissingClassException
		 */
		protected function getParameterList(ReflectionClass $reflectionClass, ReflectionMethod $reflectionMethod, string $method) {
			$parameterList = [];
			if (strlen($name = $reflectionMethod->getName()) > strlen($method) && strpos($name, $method, 0) === 0) {
				if ($reflectionMethod->isPublic() === false) {
					throw new MethodVisibilityException(sprintf('Method [%s::%s()] must be public.', $reflectionClass->getName(), $reflectionMethod->getName()));
				}
				foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
					if ($reflectionClass->hasProperty($name = $reflectionParameter->getName()) === false) {
						throw new PropertyVisibilityException(sprintf('Class [%s] must have property [$%s] of the same name as parameter in method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
					} else if (($class = $reflectionParameter->getClass()) === null) {
						throw new MissingClassException(sprintf('Class [%s] must have property [$%s] with class type hint in method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
					}
					$reflectionProperty = $reflectionClass->getProperty($name);
					$reflectionProperty->setAccessible(true);
					$parameterList[] = new Parameter($reflectionProperty->getName(), false, $class->getName());
				}
			}
			return $parameterList;
		}
	}
