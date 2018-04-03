<?php
	declare(strict_types=1);
	namespace Edde;

	use Closure;
	use Edde\Config\IConfigurable;
	use Edde\Container\ContainerException;
	use Edde\Container\IContainer;

	trait Autowire {
		protected $tInjects = [];

		/** @inheritdoc */
		public function registerAutowireDependency(string $property, IContainer $container, string $dependency, array $params = []) {
			$this->tInjects[$property] = [
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
		 * @throws Obj3ctException
		 */
		public function __get(string $name) {
			if (isset($this->tInjects[$name])) {
				/** @var $container IContainer */
				[$container, $dependency, $params] = $this->tInjects[$name];
				/** @var $instance IConfigurable */
				if (($instance = $this->{$name} = $container->create($dependency, $params, static::class)) instanceof IConfigurable && $instance->isSetup() === false) {
					$instance->setup();
				}
				return $instance;
			}
			throw new Obj3ctException(sprintf('Reading from the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		/**
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return $this
		 *
		 * @throws Obj3ctException
		 */
		public function __set(string $name, $value) {
			if (isset($this->tInjects[$name])) {
				$this->{$name} = $value;
				return $this;
			}
			throw new Obj3ctException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}
	}
