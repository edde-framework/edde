<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use IteratorAggregate;
	use stdClass;
	use Traversable;

	interface IChain extends IteratorAggregate {
		/**
		 * start a new chain
		 *
		 * @param string $name
		 *
		 * @return IChain
		 */
		public function where(string $name): IChain;

		/**
		 * @param string $name
		 *
		 * @return IChain
		 */
		public function and(string $name): IChain;

		/**
		 * @param string $name
		 *
		 * @return IChain
		 */
		public function or(string $name): IChain;

		/**
		 * @return Traversable|stdClass[]
		 */
		public function getIterator();
	}
