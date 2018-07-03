<?php
	declare(strict_types=1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\DependencyException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\ILazyInject;
	use Edde\Common\Container\AbstractFactory;
	use Edde\Common\Container\Dependency;
	use Edde\Common\Reflection\ReflectionParameter;
	use Edde\Common\Reflection\ReflectionUtils;

	class ClassFactory extends AbstractFactory {
		/**
		 * @var IDependency[]
		 */
		static protected $dependencyCache = [];

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			return class_exists($dependency) && interface_exists($dependency) === false;
		}

		protected function getParameterList(\ReflectionClass $reflectionClass, \ReflectionMethod $reflectionMethod, string $method) {
			$parameterList = [];
			if (strlen($name = $reflectionMethod->getName()) > strlen($method) && strpos($name, $method, 0) === 0) {
				if ($reflectionMethod->isPublic() === false) {
					throw new ContainerException(sprintf('Method [%s::%s()] must be public.', $reflectionClass->getName(), $reflectionMethod->getName()));
				}
				foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
					if ($reflectionClass->hasProperty($name = $reflectionParameter->getName()) === false) {
						throw new ContainerException(sprintf('Class [%s] must have property [$%s] of the same name as parameter in method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
					}
					$reflectionProperty = $reflectionClass->getProperty($name);
					$reflectionProperty->setAccessible(true);
					$parameterList[] = new ReflectionParameter($reflectionProperty->getName(), false, ($class = $reflectionParameter->getClass()) ? $class->getName() : $reflectionParameter->getName());
				}
			}
			return $parameterList;
		}

		/**
		 * @inheritdoc
		 * @throws ContainerException
		 */
		public function createDependency(IContainer $container, string $dependency = null): IDependency {
			if ($dependency === null) {
				throw new DependencyException('The $dependency parameter has not been provided for [%s], oops!', __METHOD__);
			}
			if (isset(self::$dependencyCache[$dependency])) {
				return self::$dependencyCache[$dependency];
			}
			$injectList = [];
			$lazyList = [];
			$configuratorList = [];
			foreach (ReflectionUtils::getMethodList($dependency) as $reflectionMethod) {
				$injectList = array_merge($injectList, $this->getParameterList($reflectionClass = $reflectionMethod->getDeclaringClass(), $reflectionMethod, 'inject'));
				if ($reflectionClass->implementsInterface(ILazyInject::class)) {
					$lazyList = array_merge($lazyList, $this->getParameterList($reflectionClass, $reflectionMethod, 'lazy'));
				}
			}
			$parameterList = [];
			foreach (ReflectionUtils::getParameterList($dependency) as $reflectionParameter) {
				$parameterList[] = new ReflectionParameter($reflectionParameter->getName(), $reflectionParameter->isOptional(), ($class = $reflectionParameter->getClass()) ? $class->getName() : null);
			}
			if ($dependency !== null) {
				$reflectionClass = ReflectionUtils::getReflectionClass($dependency);
				$configuratorList = array_reverse(array_merge([$dependency], $reflectionClass->getInterfaceNames()));
			}
			return self::$dependencyCache[$dependency] = new Dependency($parameterList, $injectList, $lazyList, $configuratorList);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContainer $container, array $parameterList, IDependency $dependency, string $name = null) {
			$parameterList = $this->parameters($container, $parameterList, $dependency);
			if (empty($parameterList)) {
				return new $name();
			}
			return new $name(...$parameterList);
		}
	}
