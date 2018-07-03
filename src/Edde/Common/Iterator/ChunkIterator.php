<?php
	declare(strict_types=1);

	namespace Edde\Common\Iterator;

	use Edde\Common\Object\Object;

	class ChunkIterator extends Object implements \Iterator {
		/**
		 * @var callable
		 */
		protected $chunkIteratorCallback;
		/**
		 * @var \Iterator
		 */
		protected $chunkIterator;
		/**
		 * @var \Iterator
		 */
		protected $sourceIterator;

		/**
		 * @param callable  $chunkIteratorCallback
		 * @param \Iterator $sourceIterator
		 */
		public function __construct(callable $chunkIteratorCallback, \Iterator $sourceIterator) {
			$this->chunkIteratorCallback = $chunkIteratorCallback;
			$this->sourceIterator = $sourceIterator;
		}

		public function current() {
			return $this->chunkIterator->current();
		}

		public function next() {
			$this->chunkIterator->next();
		}

		public function key() {
			return $this->chunkIterator->key();
		}

		public function valid() {
			if ($this->chunkIterator->valid() === false) {
				while ($this->sourceIterator->valid()) {
					$this->chunkIterator = call_user_func($this->chunkIteratorCallback, $this->sourceIterator->current());
					$this->sourceIterator->next();
					if ($this->chunkIterator->valid()) {
						break;
					}
				}
			}
			return $this->chunkIterator->valid();
		}

		public function rewind() {
			$this->sourceIterator->rewind();
			$this->chunkIterator = call_user_func($this->chunkIteratorCallback, $this->sourceIterator->current());
			$this->sourceIterator->next();
		}
	}
