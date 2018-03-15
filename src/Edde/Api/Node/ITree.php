<?php
	declare(strict_types=1);
	namespace Edde\Api\Node;

	use IteratorAggregate;
	use Traversable;

	/**
	 * Implementation of a common tree structure, just related to
	 */
	interface ITree extends IteratorAggregate {
		/**
		 * @param ITree $tree
		 *
		 * @return ITree
		 */
		public function add(ITree $tree): ITree;

		/**
		 * push the given node into current tree list; parent of $tree is not changed
		 *
		 * @param ITree $tree
		 *
		 * @return ITree
		 */
		public function push(ITree $tree): ITree;

		/**
		 * @return ITree[]
		 */
		public function getTrees(): array;

		/**
		 * @param ITree $tree
		 *
		 * @return ITree
		 */
		public function setParent(?ITree $tree): ITree;

		/**
		 * @return ITree|null
		 */
		public function getParent(): ?ITree;

		/**
		 * @return ITree
		 */
		public function getRoot(): ?ITree;

		/**
		 * @return bool
		 */
		public function isRoot(): bool;

		/**
		 * @return bool
		 */
		public function isLeaf(): bool;

		/**
		 * @return int
		 */
		public function getLevel(): int;

		/**
		 * return count of children
		 *
		 * @return int
		 */
		public function count(): int;

		/**
		 * @return Traversable|ITree[]
		 */
		public function getIterator();
	}
