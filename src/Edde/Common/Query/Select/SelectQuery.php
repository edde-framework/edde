<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Select;

	use Edde\Api\Node\INode;
	use Edde\Api\Query\QueryException;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractQuery;
	use Edde\Common\Query\Where\WhereExpressionFragment;

	class SelectQuery extends AbstractQuery {
		/**
		 * @var INode
		 */
		protected $selectNode;
		/**
		 * @var SelectFragment
		 */
		protected $selectPropertyFragment;
		/**
		 * @var FromFragment
		 */
		protected $fromPropertyFragment;
		/**
		 * @var WhereExpressionFragment
		 */
		protected $whereExpressionFragment;
		/**
		 * @var OrderFragment
		 */
		protected $orderFragment;

		/**
		 * @return SelectFragment
		 */
		public function select() {
			$this->use();
			return $this->selectPropertyFragment;
		}

		/**
		 * @return FromFragment
		 */
		public function from() {
			$this->use();
			return $this->fromPropertyFragment;
		}

		/**
		 * @return WhereExpressionFragment
		 */
		public function where() {
			$this->use();
			return $this->whereExpressionFragment;
		}

		/**
		 * @return OrderFragment
		 */
		public function order() {
			$this->use();
			return $this->orderFragment;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws QueryException
		 */
		public function getNode() {
			/**
			 * missing parent call is intentionall, including $this->use();
			 */
			if ($this->selectNode === null) {
				throw new QueryException(sprintf('Empty select query has no sense; please start with %s::select() method.', self::class));
			}
			return $this->selectNode;
		}

		protected function prepare() {
			$this->selectNode = new Node('select-query');
			$this->selectNode->addNodeList([
				$selectListNode = new Node('select'),
				$fromListNode = new Node('from'),
				$whereNode = new Node('where'),
				$orderNode = new Node('order'),
			]);
			$this->selectPropertyFragment = new SelectFragment($selectListNode, $this);
			$this->fromPropertyFragment = new FromFragment($fromListNode, $this);
			$this->whereExpressionFragment = new WhereExpressionFragment($whereNode);
			$this->orderFragment = new OrderFragment($orderNode, $this);
		}
	}
