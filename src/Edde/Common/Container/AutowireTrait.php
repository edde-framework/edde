<?php
	declare(strict_types=1);
	namespace Edde\Common\Container;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Object\Exception\PropertyReadException;
	use Edde\Common\Object\Exception\PropertyWriteException;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Container\FactoryException;

	trait AutowireTrait {
		protected $tAutowires = [];
		protected $tLazies = [];

		public function autowire(string $property, $dependency) {
			$this->tAutowires[$property] = $dependency;
			$this->{$property} = $dependency;
			return $this;
		}

		public function lazy(string $property, IContainer $container, string $dependency, array $params = []) {
			$this->tLazies[$property] = [
				$container,
				$dependency,
				$params,
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
		 * @throws \Edde\Exception\Container\ContainerException
		 * @throws FactoryException
		 */
		public function __get(string $name) {
			if (isset($this->tLazies[$name])) {
				/** @var $container IContainer */
				[$container, $dependency, $params] = $this->tLazies[$name];
				/** @var $instance IConfigurable */
				if (($instance = $this->{$name} = $container->create($dependency, $params, static::class)) instanceof IConfigurable && $instance->isSetup() === false) {
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
			if (isset($this->tLazies[$name])) {
				$this->{$name} = $value;
				return $this;
			}
			throw new PropertyWriteException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}
	}
