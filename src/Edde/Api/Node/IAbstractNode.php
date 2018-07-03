<?php
	declare(strict_types=1);

	namespace Edde\Api\Node;

	use Traversable;

	/**
	 * This is pure abstract interface for base (abstract) node implementation; it is not intended for direct usage.
	 */
	interface IAbstractNode {
		/**
		 * @param IAbstractNode $abstractNode
		 * @param bool          $move change $node's parent to $this
		 * @param bool          $soft
		 *
		 * @return IAbstractNode
		 */
		public function addNode(IAbstractNode $abstractNode, bool $move = false, bool $soft = false): IAbstractNode;

		/**
		 * prepend the given node to the current node list
		 *
		 * @param IAbstractNode $abstractNode
		 * @param bool          $move
		 *
		 * @return IAbstractNode
		 */
		public function prepend(IAbstractNode $abstractNode, bool $move = false): IAbstractNode;

		/**
		 * push the given node into current node list; parent of $abstractNode is not changed
		 *
		 * @param IAbstractNode $abstractNode
		 *
		 * @return IAbstractNode
		 */
		public function pushNode(IAbstractNode $abstractNode): IAbstractNode;

		/**
		 * add list of given nodes to the current node
		 *
		 * @param Traversable|IAbstractNode[] $nodeList
		 * @param bool                        $move change parent of children to the current node
		 *
		 * @return $this
		 */
		public function addNodeList($nodeList, bool $move = false);

		/**
		 * replace current list of children by the given node list
		 *
		 * @param Traversable|IAbstractNode[] $nodeList
		 * @param bool|false                  $move
		 *
		 * @return IAbstractNode
		 */
		public function setNodeList($nodeList, bool $move = false): IAbstractNode;

		/**
		 * move set of nodes to current node; if $move is true, parent of moved nodes is changed
		 *
		 * @param IAbstractNode $sourceNode children of this node will be moved
		 * @param bool|false    $move       === true, parent of children will changed to the current node
		 *
		 * @return IAbstractNode
		 */
		public function moveNodeList(IAbstractNode $sourceNode, bool $move = false): IAbstractNode;

		/**
		 * remove the given node from the list of this node; if node is not found (by object comparsion), exception is thrown
		 *
		 * @param IAbstractNode $abstractNode
		 * @param bool          $soft
		 *
		 * @return IAbstractNode
		 */
		public function removeNode(IAbstractNode $abstractNode, bool $soft = false): IAbstractNode;

		/**
		 * @return IAbstractNode[]
		 */
		public function getNodeList(): array;

		/**
		 * @return IAbstractNode
		 */
		public function clearNodeList(): IAbstractNode;

		/**
		 * @param IAbstractNode $abstractNode
		 *
		 * @return IAbstractNode
		 */
		public function setParent(IAbstractNode $abstractNode = null): IAbstractNode;

		/**
		 * @return IAbstractNode|null
		 */
		public function getParent();

		/**
		 * return list of parents in reverse order (from this node to the parents); break on the given parent if specified
		 *
		 * @param IAbstractNode $root
		 *
		 * @return IAbstractNode[]
		 */
		public function getParentList(IAbstractNode $root = null): array;

		/**
		 * @return IAbstractNode
		 */
		public function getRoot(): IAbstractNode;

		/**
		 * @return IAbstractNode[]
		 */
		public function getAncestorList(): array;

		/**
		 * @return bool
		 */
		public function isRoot(): bool;

		/**
		 * @return bool
		 */
		public function isChild(): bool;

		/**
		 * @return bool
		 */
		public function isLeaf(): bool;

		/**
		 * is this node last in the parent's node list? Throw an exception if this node is root
		 *
		 * @return bool
		 */
		public function isLast(): bool;

		/**
		 * @return int
		 */
		public function getLevel(): int;

		/**
		 * @return int
		 */
		public function getTreeHeight(): int;

		/**
		 * @return int
		 */
		public function getTreeSize(): int;

		/**
		 * check, if this node can accept given node as child ({@see self::addNode()})
		 *
		 * @param IAbstractNode $abstractNode
		 *
		 * @return bool
		 */
		public function accept(IAbstractNode $abstractNode);

		/**
		 * return count of children
		 *
		 * @return int
		 */
		public function getNodeCount(): int;

		/**
		 * insert the given node under current one (current one will have excatly one children)
		 *
		 * @param IAbstractNode $abstractNode
		 *
		 * @return IAbstractNode
		 */
		public function insert(IAbstractNode $abstractNode): IAbstractNode;

		/**
		 * @param IAbstractNode $abstractNode
		 * @param bool          $soft
		 *
		 * @return IAbstractNode return newly switched node
		 */
		public function switch (IAbstractNode $abstractNode, bool $soft = false): IAbstractNode;

		/**
		 * replace the given child node by the list of nodes
		 *
		 * @param IAbstractNode $abstractNode
		 * @param array         $nodeList
		 * @param bool          $soft
		 *
		 * @return IAbstractNode
		 */
		public function replaceNode(IAbstractNode $abstractNode, array $nodeList, bool $soft = false): IAbstractNode;
	}
