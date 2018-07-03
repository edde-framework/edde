<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Common\Object;

	class ForeachMacroIterator extends Object implements \IteratorAggregate {
		protected $iterator;
		protected $key;
		protected $value;

		public function __construct($iterator) {
			$this->iterator = $iterator;
		}

		public function k() {
			return $this->key;
		}

		public function v() {
			return $this->value;
		}

		public function getIterator() {
			foreach ($this->iterator as $k => $v) {
				yield ($this->key = $k) => ($this->value = $v);
			}
		}
	}
