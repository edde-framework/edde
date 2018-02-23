<?php
	declare(strict_types=1);
	namespace Edde\Common\Container;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Object\Exception\PropertyReadException;
	use Edde\Common\Object\Exception\PropertyWriteException;

	trait AutowireTrait {
		protected $tAutowireList = [];
		protected $tLazyList = [];

		public function autowire(string $property, $dependency) {
			$this->tAutowireList[$property] = $dependency;
			$this->{$property} = $dependency;
			return $this;
		}

		public function lazy(string $property, IContainer $container, string $dependency, array $parameterList = []) {
			$this->tLazyList[$property] = [
				$container,
				$dependency,
				$parameterList,
			];
			call_user_func(\Closure::bind(function (string $property) {
				unset($this->{$property});
			}, $this, static::class), $property);
			return $this;
		}

		/**
		 * @param string $name
		 *
		 * @return mixed
		 * @throws PropertyReadException
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function __get(string $name) {
			if (isset($this->tLazyList[$name])) {
				/** @var $container IContainer */
				[$container, $dependency, $parameters] = $this->tLazyList[$name];
				/** @var $instance IConfigurable */
				if (($instance = $this->{$name} = $container->create($dependency, $parameters, static::class)) instanceof IConfigurable && $instance->isSetup() === false) {
					$instance->setup();
				}
				return $instance;
			}
			throw new PropertyReadException(sprintf('Reading from the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		/**
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return $this
		 * @throws PropertyWriteException
		 */
		public function __set(string $name, $value) {
			if (isset($this->tLazyList[$name])) {
				$this->{$name} = $value;
				return $this;
			}
			throw new PropertyWriteException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}
	}
