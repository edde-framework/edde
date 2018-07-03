<?php
	declare(strict_types=1);

	namespace Edde\Common\Callback;

	use Edde\Api\Callback\IParameter;
	use Edde\Common\Object;

	class Parameter extends Object implements IParameter {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $class;
		/**
		 * @var bool
		 */
		protected $optional;

		/**
		 * @param string $name
		 * @param string $class
		 * @param bool   $optional
		 */
		public function __construct($name, $class, $optional) {
			$this->name = $name;
			$this->class = $class;
			$this->optional = $optional;
		}

		public function getName() {
			return $this->name;
		}

		public function hasClass() {
			return $this->class !== null;
		}

		public function getClass() {
			return $this->class;
		}

		public function isOptional() {
			return $this->optional;
		}
	}
