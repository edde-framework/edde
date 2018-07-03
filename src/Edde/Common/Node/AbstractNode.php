<?php
	declare(strict_types = 1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\IAbstractNode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\AbstractObject;

	/**
	 * Pure node tree implementation; this class holds all common methods for node manipulation.
	 */
	abstract class AbstractNode extends AbstractObject implements IAbstractNode {
		/**
		 * @var IAbstractNode
		 */
		protected $parent;
		/**
		 * @var IAbstractNode[]
		 */
		protected $nodeList = [];
		/**
		 * @var int
		 */
		protected $level;

		/**
		 * State-of-the-art: any computer you can't afford.
		 *
		 * @param IAbstractNode|null $parent
		 */
		public function __construct(IAbstractNode $parent = null) {
			$this->parent = $parent;
		}

		/**
		 * @inheritdoc
		 */
		public function getRoot(): IAbstractNode {
			$parent = $this;
			foreach ($this->getParentList() as $parent) {
				;
			}
			return $parent;
		}

		/**
		 * @inheritdoc
		 */
		public function getParentList(IAbstractNode $root = null): array {
			$parent = $this->getParent();
			$parentList[] = $root ?: $parent;
			while ($parent && $parent !== $root) {
				$parentList[] = $parent;
				$parent = $parent->getParent();
			}
			return $parentList;
		}

		/**
		 * @inheritdoc
		 */
		public function getParent() {
			return $this->parent;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function setParent(IAbstractNode $abstractNode = null): IAbstractNode {
			if ($abstractNode !== null && $abstractNode->accept($this) === false) {
				throw new NodeException(sprintf("Cannot set parent for [%s]: parent [%s] doesn't accept this node.", static::class, get_class($abstractNode)));
			}
			$this->parent = $abstractNode;
			$this->level = null;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isChild(): bool {
			return $this->getParent() !== null;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function addNodeList($nodeList, bool $move = false) {
			foreach ($nodeList as $node) {
				$this->addNode($node, $move);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function addNode(IAbstractNode $abstractNode, bool $move = false, bool $soft = false): IAbstractNode {
			if ($this->accept($abstractNode) === false) {
				throw new NodeException(sprintf("Current node [%s] doesn't accept given node [%s].", static::class, get_class($abstractNode)));
			}
			/** @var $parent IAbstractNode */
			$parent = $abstractNode->getParent();
			if ($move || $parent === null) {
				if ($parent) {
					$parent->removeNode($abstractNode, $soft);
				}
				$abstractNode->setParent($this);
			}
			$this->nodeList[] = $abstractNode;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function prepend(IAbstractNode $abstractNode, bool $move = false): IAbstractNode {
			/** @var $parent IAbstractNode */
			$parent = $abstractNode->getParent();
			if ($move || $parent === null) {
				if ($parent) {
					$parent->removeNode($abstractNode);
				}
				$abstractNode->setParent($this);
			}
			$this->nodeList = array_merge([$abstractNode], $this->nodeList);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function pushNode(IAbstractNode $abstractNode): IAbstractNode {
			if ($this->accept($abstractNode) === false) {
				throw new NodeException(sprintf("Current node [%s] doesn't accept given node [%s].", static::class, get_class($abstractNode)));
			}
			$this->nodeList[] = $abstractNode;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function moveNodeList(IAbstractNode $sourceNode, bool $move = false): IAbstractNode {
			foreach ($sourceNode->getNodeList() as $node) {
				$sourceNode->removeNode($node);
				$this->addNode($node, $move);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function removeNode(IAbstractNode $abstractNode, bool $soft = false): IAbstractNode {
			foreach ($this->nodeList as $index => $node) {
				if ($node === $abstractNode) {
					$node->setParent(null);
					unset($this->nodeList[$index]);
					return $this;
				}
			}
			if ($soft) {
				return $this;
			}
			throw new NodeException('The given node is not in current node list.');
		}

		/**
		 * @inheritdoc
		 */
		public function clearNodeList(): IAbstractNode {
			foreach ($this->nodeList as $node) {
				$node->setParent(null);
			}
			$this->nodeList = [];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getAncestorList(): array {
			$ancestorList = [];
			$node = $this;
			while ($parent = $node->getParent()) {
				array_unshift($ancestorList, $parent);
				$node = $parent;
			}
			return $ancestorList;
		}

		/**
		 * @inheritdoc
		 */
		public function getLevel(): int {
			if ($this->level !== null) {
				return $this->level;
			}
			$this->level = 0;
			$node = $this;
			while ($parent = $node->getParent()) {
				$this->level++;
				$node = $parent;
			}
			return $this->level;
		}

		/**
		 * @inheritdoc
		 */
		public function getTreeHeight(): int {
			if ($this->isLeaf()) {
				return 0;
			}
			$heightList = [];
			foreach ($this->nodeList as $node) {
				$heightList[] = $node->getTreeHeight();
			}
			return max($heightList) + 1;
		}

		/**
		 * @inheritdoc
		 */
		public function isLeaf(): bool {
			return count($this->nodeList) === 0;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function isLast(): bool {
			if ($this->isRoot()) {
				throw new NodeException(sprintf('Cannot check last flag of root node.'));
			}
			$nodeList = $this->getParent()
				->getNodeList();
			return end($nodeList) === $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isRoot(): bool {
			return $this->getParent() === null;
		}

		/**
		 * @inheritdoc
		 */
		public function getTreeSize(): int {
			$size = 1;
			foreach ($this->nodeList as $node) {
				$size += $node->getTreeSize();
			}
			return $size;
		}

		/**
		 * @inheritdoc
		 */
		public function getNodeCount(): int {
			return count($this->nodeList);
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function insert(IAbstractNode $abstractNode): IAbstractNode {
			if ($abstractNode->isLeaf() === false) {
				throw new NodeException('Node must be empty.');
			}
			$this->addNode($abstractNode->addNodeList($this->getNodeList(), true));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getNodeList(): array {
			return $this->nodeList;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function setNodeList($nodeList, bool $move = false): IAbstractNode {
			$this->nodeList = [];
			foreach ($nodeList as $node) {
				$this->addNode($node, $move);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function switch (IAbstractNode $abstractNode, bool $soft = false): IAbstractNode {
			if (($parent = $this->getParent()) !== null) {
				$parent->replaceNode($this, [$abstractNode], $soft);
			}
			$abstractNode->addNode($this);
			$abstractNode->setParent($parent);
			$this->setParent($abstractNode);
			return $abstractNode;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function replaceNode(IAbstractNode $abstractNode, array $nodeList, bool $soft = false): IAbstractNode {
			if (($index = array_search($abstractNode, $this->nodeList, true)) === false || $abstractNode->getParent() !== $this) {
				if ($soft) {
					return $this;
				}
				throw new NodeException(sprintf('Cannot replace the given node in root; root is not parent of the given node.'));
			}
			array_splice($this->nodeList, $index, 0, $nodeList);
			unset($this->nodeList[array_search($abstractNode, $this->nodeList, true)]);
			foreach ($nodeList as $node) {
				$node->setParent($this);
			}
			return $this;
		}

		public function __clone() {
			foreach ($this->nodeList as &$node) {
				$node = clone $node;
			}
			unset($node);
		}
	}
