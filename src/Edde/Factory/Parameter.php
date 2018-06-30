<?php
	declare(strict_types=1);
	namespace Edde\Factory;

	use Edde\SimpleObject;

	class Parameter extends SimpleObject implements IParameter {
		/** @var string */
		protected $name;
		/** @var string */
		protected $class;

		/**
		 * @param string $name
		 * @param string $class
		 */
		public function __construct(string $name, string $class) {
			$this->name = $name;
			$this->class = $class;
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name;
		}

		/** @inheritdoc */
		public function getClass(): string {
			return $this->class;
		}
	}
