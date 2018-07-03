<?php
	declare(strict_types=1);

	namespace Edde\Common\Iterator;

	use Edde\Api\Iterator\IIterator;
	use Edde\Common\Object;

	class Iterator extends Object implements IIterator {
		/**
		 * @var \Iterator
		 */
		protected $iterator;
		/**
		 * @var bool
		 */
		protected $continue;
		/**
		 * @var bool
		 */
		protected $skipNext;

		/**
		 * @param \Iterator $iterator
		 */
		public function __construct(\Iterator $iterator) {
			$this->iterator = $iterator;
		}

		public function setContinue(bool $continue = true): IIterator {
			$this->continue = $continue;
			return $this;
		}

		public function setSkipNext(bool $skipNext = true): IIterator {
			$this->skipNext = $skipNext;
			return $this;
		}

		public function current() {
			return $this->iterator->current();
		}

		public function next() {
			if ($this->skipNext) {
				$this->skipNext = false;
				return;
			}
			$this->iterator->next();
		}

		public function key() {
			return $this->iterator->key();
		}

		public function valid() {
			return $this->iterator->valid();
		}

		public function rewind() {
			if ($this->continue) {
				$this->continue = false;
				return;
			}
			$this->iterator->rewind();
		}
	}
