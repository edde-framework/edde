<?php
	declare(strict_types = 1);

	namespace Edde\Common\Node;

	use ArrayIterator;
	use Edde\Api\Node\IAbstractNode;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\AbstractObject;
	use RecursiveIterator;
	use RecursiveIteratorIterator;

	/**
	 * Iterator over nodes support with helper classes for recursive iterator, ...
	 */
	class NodeIterator extends AbstractObject implements RecursiveIterator {
		/**
		 * @var IAbstractNode
		 */
		protected $node;
		/**
		 * @var \Iterator
		 */
		protected $iterator;

		/**
		 * The man approached the very beautiful woman in the large supermarket and asked,
		 * “You know, I’ve lost my wife here in the supermarket. Can you talk to me for a couple of minutes?”
		 * “Why?”
		 * “Because every time I talk to a beautiful woman my wife appears out of nowhere.”
		 *
		 * @param IAbstractNode $node
		 */
		public function __construct(IAbstractNode $node) {
			$this->node = $node;
		}

		/**
		 * @param IAbstractNode $abstractNode
		 * @param bool $root
		 *
		 * @return RecursiveIteratorIterator|INode[]
		 * @throws NodeException
		 */
		static public function recursive(IAbstractNode $abstractNode, bool $root = false): RecursiveIteratorIterator {
			if ($root === true) {
				$rootNode = new Node();
				$rootNode->pushNode($abstractNode);
				$abstractNode = $rootNode;
			}
			return new RecursiveIteratorIterator(new self($abstractNode), RecursiveIteratorIterator::SELF_FIRST);
		}

		/**
		 * @inheritdoc
		 */
		public function next() {
			$this->iterator->next();
		}

		/**
		 * @inheritdoc
		 */
		public function key() {
			return $this->iterator->key();
		}

		/**
		 * @inheritdoc
		 */
		public function valid() {
			return $this->iterator->valid();
		}

		/**
		 * @inheritdoc
		 */
		public function rewind() {
			$this->iterator = new ArrayIterator($this->node->getNodeList());
			$this->iterator->rewind();
		}

		/**
		 * @inheritdoc
		 */
		public function hasChildren() {
			$current = $this->current();
			return $current->isLeaf() === false;
		}

		/**
		 * @inheritdoc
		 */
		public function current() {
			return $this->iterator->current();
		}

		/**
		 * @inheritdoc
		 */
		public function getChildren() {
			return new self($this->current());
		}
	}
