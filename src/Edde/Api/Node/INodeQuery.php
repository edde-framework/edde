<?php
	declare(strict_types=1);

	namespace Edde\Api\Node;

	use Iterator;

	interface INodeQuery {
		/**
		 * run query against source of nodes
		 *
		 * @param Iterator $iterator
		 *
		 * @return Iterator
		 */
		public function query(Iterator $iterator);

		/**
		 * return iterator over filtered nodes (method will build RecursiveIterator over the given node)
		 *
		 * @param INode $node
		 *
		 * @return Iterator|INode[]
		 */
		public function filter(INode $node);

		/**
		 * has the given node tree some results from this query?
		 *
		 * @param INode $node
		 *
		 * @return bool
		 */
		public function isEmpty(INode $node);
	}
