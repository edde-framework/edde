<?php
	declare(strict_types=1);
	namespace Edde\Container\Factory;

	use Closure;
	use Edde\Container\IContainer;
	use Edde\Container\IReflection;
	use Edde\Container\Parameter;
	use Edde\Container\Reflection;
	use ReflectionException;
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
		 * @param callable $callback
		 * @param string   $name
		 *
		 * @throws ReflectionException
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
			$params = [];
			foreach ($this->reflectionFunction->getParameters() as $reflectionParameter) {
				if (($parameterReflectionClass = $reflectionParameter->getClass()) === null) {
					break;
				}
				$params[] = new Parameter($reflectionParameter->getName(), $reflectionParameter->isOptional(), $parameterReflectionClass->getName());
			}
			return new Reflection($params);
		}

		/**
		 * @inheritdoc
		 */
		public function factory(IContainer $container, array $params, IReflection $dependency, string $name = null) {
			$callback = $this->callback;
			return $callback(...$this->params($container, $params, $dependency));
		}
	}
