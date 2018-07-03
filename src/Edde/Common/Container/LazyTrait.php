<?php
	declare(strict_types=1);

	namespace Edde\Common\Container;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Container\IContainer;
	use Edde\Api\EddeException;

	trait LazyTrait {
		protected $aInjectList = [];
		protected $aLazyInjectList = [];

		public function inject(string $property, $dependency) {
			$this->aInjectList[$property] = $dependency;
			$this->{$property} = $dependency;
			return $this;
		}

		public function lazy(string $property, IContainer $container, string $dependency, array $parameterList = []) {
			$this->aLazyInjectList[$property] = [
				$container,
				$dependency,
				$parameterList,
			];
			call_user_func(\Closure::bind(function (string $property) {
				/** @noinspection PhpVariableVariableInspection */
				unset($this->$property);
			}, $this, static::class), $property);
			return $this;
		}

		/**
		 * @param string $name
		 *
		 * @return mixed
		 * @throws EddeException
		 */
		public function __get(string $name) {
			if (isset($this->aLazyInjectList[$name])) {
				/** @var $container IContainer */
				list($container, $dependency, $parameterList) = $this->aLazyInjectList[$name];
				/** @var $instance IConfigurable */
				if (($instance = $this->$name = $container->create($dependency, $parameterList, static::class)) instanceof IConfigurable) {
					$instance->setup();
				}
				return $instance;
			}
			throw new EddeException(sprintf('Reading from the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		/**
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return $this
		 * @throws EddeException
		 */
		public function __set(string $name, $value) {
			if (isset($this->aLazyInjectList[$name])) {
				/** @noinspection PhpVariableVariableInspection */
				$this->$name = $value;
				return $this;
			}
			throw new EddeException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}
	}
