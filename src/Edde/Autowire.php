<?php
	declare(strict_types=1);
	namespace Edde;

	use Closure;
	use Edde\Config\IConfigurable;
	use Edde\Container\ContainerException;
	use Edde\Container\IContainer;

	trait Autowire {
		protected $tAutowires = [];
		protected $tLazies = [];

		/** @inheritdoc */
		public function autowire(string $property, $dependency) {
			$this->tAutowires[$property] = $dependency;
			$this->{$property} = $dependency;
			return $this;
		}

		/** @inheritdoc */
		public function lazy(string $property, IContainer $container, string $dependency, array $params = []) {
			$this->tLazies[$property] = [
				$container,
				$dependency,
				$params,
			];
			call_user_func(Closure::bind(function (string $property) {
				unset($this->{$property});
			}, $this, static::class), $property);
			return $this;
		}

		/**
		 * @param string $name
		 *
		 * @return IConfigurable
		 *
		 * @throws ContainerException
		 * @throws ObjectException
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
			throw new ObjectException(sprintf('Reading from the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		/**
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return $this
		 *
		 * @throws ObjectException
		 */
		public function __set(string $name, $value) {
			if (isset($this->tLazies[$name])) {
				$this->{$name} = $value;
				return $this;
			}
			throw new ObjectException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}
	}