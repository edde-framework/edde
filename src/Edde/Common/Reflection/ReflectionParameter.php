<?php
	declare(strict_types=1);

	namespace Edde\Common\Reflection;

	use Edde\Api\Reflection\IReflectionParameter;
	use Edde\Common\Object;

	class ReflectionParameter extends Object implements IReflectionParameter {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var bool
		 */
		protected $optional;
		/**
		 * @var string
		 */
		protected $class;

		/**
		 * @param string $name
		 * @param string $class
		 * @param bool   $optional
		 */
		public function __construct(string $name, bool $optional, string $class = null) {
			$this->name = $name;
			$this->optional = $optional;
			$this->class = $class;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function isOptional(): bool {
			return $this->optional;
		}

		public function getClass() {
			return $this->class;
		}
	}
