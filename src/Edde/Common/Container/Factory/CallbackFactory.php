<?php
	declare(strict_types=1);
	namespace Edde\Common\Container\Factory;

	use Closure;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IReflection;
	use Edde\Common\Container\Parameter;
	use Edde\Common\Container\Reflection;
	use ReflectionFunction;

	class CallbackFactory extends AbstractFactory {
		/**
		 * @var callable
		 */
		protected $callback;
		/**
		 * @var ReflectionFunction
		 */
		protected $reflectionFunction;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param string   $name
		 * @param callable $callback
		 */
		public function __construct(callable $callback, string $name = null) {
			$this->reflectionFunction = new ReflectionFunction(Closure::fromCallable($this->callback = $callback));
			$this->name = $name ?: (string)$this->reflectionFunction->getReturnType();
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			return $dependency === $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function getReflection(IContainer $container, string $dependency): IReflection {
			$parameterList = [];
			foreach ($this->reflectionFunction->getParameters() as $reflectionParameter) {
				if (($parameterReflectionClass = $reflectionParameter->getClass()) === null) {
					break;
				}
				$parameterList[] = new Parameter($reflectionParameter->getName(), $reflectionParameter->isOptional(), $parameterReflectionClass->getName());
			}
			return new Reflection($parameterList);
		}

		/**
		 * @inheritdoc
		 */
		public function factory(IContainer $container, array $parameterList, IReflection $dependency, string $name = null) {
			$callback = $this->callback;
			return $callback(...$this->parameters($container, $parameterList, $dependency));
		}
	}
