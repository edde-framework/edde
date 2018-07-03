<?php
	declare(strict_types=1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\ITreeTraversal;
	use Edde\Common\Object;

	/**
	 * A new teacher was trying to make use of her psychology courses.
	 * She started her class by saying, "Everyone who thinks they're stupid, stand up!"
	 * After a few seconds, Little Johnny stood up.
	 * The teacher said, "Do you think you're stupid, Little Johnny?"
	 * "No, ma'am, but I hate to see you standing there all by yourself!"
	 */
	abstract class AbstractTreeTraversal extends Object implements ITreeTraversal {
		/**
		 * @inheritdoc
		 */
		public function select(INode $node, ...$parameters): ITreeTraversal {
			return $this;
		}

		public function traverse(INode $node, \Iterator $iterator, ...$parameters) {
			$treeTraversal = $this->select($node, ...$parameters);
			try {
				$treeTraversal->enter($node, $iterator, ...$parameters);
				$treeTraversal->node($node, $iterator, ...$parameters);
				$treeTraversal->leave($node, $iterator, ...$parameters);
			} catch (SkipException $exception) {
				/**
				 * skip exception could be safely ignored
				 */
			}
		}

		/**
		 * @inheritdoc
		 */
		public function enter(INode $node, \Iterator $iterator, ...$parameters) {
		}

		/**
		 * @inheritdoc
		 */
		public function node(INode $node, \Iterator $iterator, ...$parameters) {
			$level = $node->getLevel();
			$stack = new \SplStack();
			/**
			 * @var $levelTreeTraversal ITreeTraversal
			 * @var $levelCurrent       INode
			 * @var $current            INode
			 */
			while ($iterator->valid() && $current = $iterator->current()) {
				/**
				 * we are out ot current subtree
				 */
				if (($currentLevel = $current->getLevel()) <= $level) {
					break;
				}
				$treeTraversal = $this->select($current, ...$parameters);
				foreach ($stack as list($levelTreeTraversal, $levelCurrent)) {
					if ($levelCurrent->getLevel() < $currentLevel) {
						break;
					}
					$levelTreeTraversal->leave($levelCurrent, $iterator, $parameters);
					$stack->pop();
				}
				if ($current->isLeaf() === false) {
					$stack->push([
						$treeTraversal,
						$current,
					]);
				}
				try {
					$treeTraversal->enter($current, $iterator, ...$parameters);
					$treeTraversal->node($current, $iterator, ...$parameters);
					if ($current->isLeaf()) {
						$treeTraversal->leave($current, $iterator, $parameters);
					}
					$iterator->next();
				} catch (SkipException $exception) {
					/**
					 * skip exception could be safely ignored
					 */
					if ($current->isLeaf() === false) {
						$stack->pop();
					}
				}
			}
			while ($stack->isEmpty() === false) {
				list($levelTreeTraversal, $levelCurrent) = $stack->pop();
				$levelTreeTraversal->leave($levelCurrent, $iterator, $parameters);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function leave(INode $node, \Iterator $iterator, ...$parameters) {
		}
	}
