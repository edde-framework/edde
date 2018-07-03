<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Where;

	use Edde\Api\Node\INode;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractFragment;

	class WhereFragment extends AbstractFragment {
		/**
		 * @var WhereExpressionFragment
		 */
		protected $whereExpressionFragment;

		/**
		 * @param INode $whereNode
		 * @param WhereExpressionFragment $whereExpressionFragment
		 */
		public function __construct(INode $whereNode, WhereExpressionFragment $whereExpressionFragment) {
			parent::__construct($whereNode);
			$this->whereExpressionFragment = $whereExpressionFragment;
		}

		/**
		 * select property of a schema; it's something similar to a column
		 *
		 * @param string $property
		 * @param string|null $prefix
		 * @param string|null $alias
		 *
		 * @return $this
		 */
		public function property($property, $prefix = null, $alias = null) {
			$this->node->addNode(new Node('property', $property, [
				'alias' => $alias,
				'prefix' => $prefix,
			]));
			return $this;
		}

		/**
		 * input is parameter; parameters can be extracted and used for bound (in case of RDBMS supporting parameter binding)
		 *
		 * @param mixed $parameter
		 * @param string|null $name
		 *
		 * @return $this
		 */
		public function parameter($parameter, $name = null) {
			$this->node->addNode(new Node('parameter', $parameter, [
				'name' => $name ?: hash('sha256', spl_object_hash($this)),
			]));
			return $this;
		}

		/** @noinspection PhpMethodNamingConventionInspection */
		public function eq() {
			return $this->whereExpressionFragment->eq();
		}

		/** @noinspection PhpMethodNamingConventionInspection */
		public function neq() {
			return $this->whereExpressionFragment->neq();
		}

		/** @noinspection PhpMethodNamingConventionInspection */
		public function gt() {
			return $this->whereExpressionFragment->gt();
		}

		public function gte() {
			return $this->whereExpressionFragment->gte();
		}

		/** @noinspection PhpMethodNamingConventionInspection */
		public function lt() {
			return $this->whereExpressionFragment->lt();
		}

		public function lte() {
			return $this->whereExpressionFragment->lte();
		}

		/**
		 * @return WhereFragment
		 */
		public function isNull() {
			return $this->whereExpressionFragment->isNull();
		}

		/**
		 * @return WhereFragment
		 */
		public function isNotNull() {
			return $this->whereExpressionFragment->isNotNull();
		}

		/**
		 * @return WhereExpressionFragment
		 */
		public function and () {
			return $this->whereExpressionFragment->and();
		}

		/** @noinspection PhpMethodNamingConventionInspection */
		/**
		 * @return WhereExpressionFragment
		 */
		public function or () {
			return $this->whereExpressionFragment->or();
		}

		/**
		 * @return WhereExpressionFragment
		 */
		public function group() {
			return $this->whereExpressionFragment->group();
		}
	}
