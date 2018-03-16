<?php
	declare(strict_types=1);
	namespace Edde\Common\Node;

	use ArrayIterator;
	use Edde\Node\ITree;
	use Edde\Object;

	/**
	 * Pure node tree implementation; this class holds all common methods for node manipulation.
	 */
	class Tree extends Object implements ITree {
		/** @var \Edde\Node\ITree */
		protected $parent;
		/** @var \Edde\Node\ITree[] */
		protected $trees = [];
		/** @var int */
		protected $level;

		/**
		 * State-of-the-art: any computer you can't afford.
		 *
		 * @param \Edde\Node\ITree|null $parent
		 */
		public function __construct(\Edde\Node\ITree $parent = null) {
			$this->parent = $parent;
		}

		/** @inheritdoc */
		public function add(ITree $tree): \Edde\Node\ITree {
			if ($tree->isRoot()) {
				$tree->setParent($this);
			}
			$this->trees[] = $tree;
			return $this;
		}

		/** @inheritdoc */
		public function push(\Edde\Node\ITree $tree): ITree {
			$this->trees[] = $tree;
			return $this;
		}

		/** @inheritdoc */
		public function getRoot(): ?ITree {
			$parent = $this;
			/** @noinspection PhpStatementHasEmptyBodyInspection */
			while ($parent = $parent->getParent()) {
				;
			}
			return $parent;
		}

		/** @inheritdoc */
		public function getParent(): ?ITree {
			return $this->parent;
		}

		/** @inheritdoc */
		public function setParent(?\Edde\Node\ITree $tree): \Edde\Node\ITree {
			$this->parent = $tree;
			$this->level = null;
			return $this;
		}

		/** @inheritdoc */
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

		/** @inheritdoc */
		public function isLeaf(): bool {
			return count($this->trees) === 0;
		}

		/** @inheritdoc */
		public function isRoot(): bool {
			return $this->getParent() === null;
		}

		/** @inheritdoc */
		public function count(): int {
			return count($this->trees);
		}

		/** @inheritdoc */
		public function getTrees(): array {
			return $this->trees;
		}

		/** @inheritdoc */
		public function getIterator() {
			return new ArrayIterator($this->trees);
		}

		/** @inheritdoc */
		public function __clone() {
			parent::__clone();
			foreach ($this->trees as &$node) {
				$node = clone $node;
			}
			unset($node);
		}
	}
