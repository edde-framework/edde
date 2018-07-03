<?php
	declare(strict_types=1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Common\Container\Dependency;

	/**
	 * Interface to class binding factory.
	 */
	class InterfaceFactory extends ClassFactory {
		/**
		 * @var string
		 */
		protected $interface;
		/**
		 * @var string
		 */
		protected $class;
		/**
		 * @var mixed
		 */
		protected $instance;

		/**
		 * Practical thought:
		 * A husband is supposed to make his wife's panties wet, not her eyes.
		 * A wife is supposed to make her husband's dick hard, not his life...!
		 *
		 * @param string $interface
		 * @param string $class
		 */
		public function __construct(string $interface, string $class) {
			$this->interface = $interface;
			$this->class = $class;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			return $dependency === $this->interface;
		}

		/**
		 * @inheritdoc
		 */
		public function createDependency(IContainer $container, string $dependency = null): IDependency {
			if ($this->instance) {
				return new Dependency();
			}
			return parent::createDependency($container, $this->class);
		}

		/**
		 * @inheritdoc
		 */
		public function fetch(IContainer $container, string $id) {
			return $this->instance;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContainer $container, array $parameterList, IDependency $dependency, string $name = null) {
			return $this->instance ?: $this->instance = parent::execute($container, $parameterList, $dependency, $this->class);
		}
	}
