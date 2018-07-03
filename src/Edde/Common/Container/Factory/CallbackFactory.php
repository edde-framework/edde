<?php
	declare(strict_types=1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Reflection\ReflectionException;
	use Edde\Common\Container\Dependency;
	use Edde\Common\Reflection\ReflectionParameter;
	use Edde\Common\Reflection\ReflectionUtils;

	class CallbackFactory extends AbstractFactory {
		/**
		 * @var callable
		 */
		protected $callback;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param string   $name
		 * @param callable $callback
		 */
		public function __construct(callable $callback, string $name = null) {
			$this->callback = $callback;
			$this->name = $name;
		}

		/**
		 * @inheritdoc
		 * @throws ReflectionException
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			if ($this->name === null) {
				$this->name = (string)ReflectionUtils::getMethodReflection($this->callback)->getReturnType();
			}
			return $dependency === $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function createDependency(IContainer $container, string $dependency = null): IDependency {
			$parameterList = [];
			foreach (ReflectionUtils::getParameterList($this->callback) as $reflectionParameter) {
				$parameterList[] = new ReflectionParameter($reflectionParameter->getName(), $reflectionParameter->isOptional(), ($class = $reflectionParameter->getClass()) ? $class->getName() : null);
			}
			return new Dependency($parameterList);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContainer $container, array $parameterList, IDependency $dependency, string $name = null) {
			return call_user_func_array($this->callback, $this->parameters($container, $parameterList, $dependency));
		}
	}
