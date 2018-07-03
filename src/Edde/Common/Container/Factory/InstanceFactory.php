<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;

	/**
	 * Factory for holding already existing instances.
	 */
	class InstanceFactory extends AbstractFactory {
		/**
		 * @param string $name
		 * @param mixed $instance
		 */
		public function __construct(string $name, $instance) {
			parent::__construct($name);
			$this->instance = $instance;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterList(string $name = null): array {
			/**
			 * instance is already living, so no parameters are needed
			 */
			return [];
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function deffered(callable $callback): IFactory {
			throw new FactoryException(sprintf('Cannot register deffered handler on [%s]; setup handlers are not supported by this factory.', self::class));
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function create(string $name, array $parameterList, IContainer $container) {
			return $this->instance;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function factory(string $name, array $parameterList, IContainer $container) {
			throw new FactoryException('Something went wrong. God will kill one cute kitten and The Deep Evil of The Most Evilest Hell will eat it!');
		}
	}
