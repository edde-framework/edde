<?php
	declare(strict_types=1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Common\Container\Dependency;

	/**
	 * This factory will create singleton instance of the given class.
	 */
	class InstanceFactory extends ClassFactory {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $class;
		/**
		 * @var array
		 */
		protected $parameterList;
		/**
		 * @var mixed
		 */
		protected $instance;
		/**
		 * @var bool
		 */
		protected $cloneable;

		/**
		 * Little Billy came home from school to see the families pet rooster dead in the front yard.
		 * Rigor mortis had set in and it was flat on its back with its legs in the air.
		 * When his Dad came home Billy said, "Dad our roosters dead and his legs are sticking in the air. Why are his legs sticking in the air?"
		 * His father thinking quickly said, "Son, that's so God can reach down from the clouds and lift the rooster straight up to heaven."
		 * "Gee Dad that's great," said little Billy.
		 * A few days later, when Dad came home from work, Billy rushed out to meet him yelling, "Dad, Dad we almost lost Mom today!"
		 * "What do you mean?" said Dad.
		 * "Well Dad, I got home from school early today and went up to your bedroom and there was Mom flat on her back with her legs in the air screaming, "Jesus I'm coming, I'm coming" If it hadn't of been for Uncle George holding her down we'd have lost her for sure!"
		 *
		 * @param string     $name
		 * @param string     $class
		 * @param array      $parameterList
		 * @param mixed|null $instance
		 * @param bool       $cloneable
		 */
		public function __construct(string $name, string $class, array $parameterList = [], $instance = null, bool $cloneable = false) {
			$this->name = $name;
			$this->class = $class;
			$this->parameterList = $parameterList;
			$this->instance = $instance;
			$this->cloneable = $cloneable;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			return $this->name === $dependency;
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
			return $this->cloneable && $this->instance ? clone $this->instance : $this->instance;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContainer $container, array $parameterList, IDependency $dependency, string $name = null) {
			if ($this->instance === null) {
				$this->instance = $container->dependency($this->instance = parent::execute($container, $this->parameterList, $dependency, $this->class), $dependency);
			}
			/**
			 * immediate clone is necessary because otherwise base class could be (surprisingly) changed
			 */
			return $this->cloneable ? clone $this->instance : $this->instance;
		}
	}
