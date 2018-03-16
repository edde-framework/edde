<?php
	declare(strict_types=1);
	namespace Edde\Container;

	use Edde\Object;

	class Parameter extends Object implements IParameter {
		/** @var string */
		protected $name;
		/** @var bool */
		protected $optional;
		/** @var string */
		protected $class;

		/**
		 * @param string $name
		 * @param string $class
		 * @param bool   $optional
		 */
		public function __construct(string $name, bool $optional, string $class) {
			$this->name = $name;
			$this->optional = $optional;
			$this->class = $class;
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name;
		}

		/** @inheritdoc */
		public function isOptional(): bool {
			return $this->optional;
		}

		/** @inheritdoc */
		public function getClass(): string {
			return $this->class;
		}
	}
