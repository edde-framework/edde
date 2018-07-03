<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Select;

	use Edde\Api\Node\INode;
	use Edde\Common\Deffered\DefferedTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractFragment;

	class FromFragment extends AbstractFragment {
		use DefferedTrait;
		/**
		 * @var SelectQuery
		 */
		protected $selectQuery;
		/**
		 * @var JoinFragment
		 */
		protected $joinFragment;

		public function __construct(INode $node, SelectQuery $selectQuery) {
			parent::__construct($node);
			$this->selectQuery = $selectQuery;
		}

		/**
		 * @return SelectFragment
		 */
		public function select() {
			return $this->selectQuery->select();
		}

		/**
		 * @param string $source
		 * @param string|null $alias
		 *
		 * @return FromFragment
		 */
		public function source($source, $alias = null) {
			$this->node->addNode(new Node('source', $source, [
				'alias' => $alias,
			]));
			return $this;
		}

		/**
		 * @return JoinFragment
		 */
		public function join() {
			return $this->joinFragment;
		}

		public function where() {
			return $this->selectQuery->where();
		}

		/**
		 * @return OrderFragment
		 */
		public function order() {
			return $this->selectQuery->order();
		}

		protected function prepare() {
			$this->joinFragment = new JoinFragment($this->node);
		}
	}
