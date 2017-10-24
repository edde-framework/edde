<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\Fragment\IWhereIn;
		use Edde\Api\Query\Fragment\IWhereThan;
		use Edde\Api\Query\Fragment\IWhereTo;
		use Edde\Common\Node\Node;

		class WhereFragment extends AbstractFragment implements IWhere {
			/**
			 * @inheritdoc
			 */
			public function eq(string $name): IWhereTo {
				return $this->createToFragment(__FUNCTION__, $name);
			}

			/**
			 * @inheritdoc
			 */
			public function neq(string $name): IWhereTo {
				return $this->createToFragment(__FUNCTION__, $name);
			}

			/**
			 * @inheritdoc
			 */
			public function gt(string $name): IWhereThan {
				return $this->createThanFragment(__FUNCTION__, $name);
			}

			/**
			 * @inheritdoc
			 */
			public function gte(string $name): IWhereThan {
				return $this->createThanFragment(__FUNCTION__, $name);
			}

			/**
			 * @inheritdoc
			 */
			public function lt(string $name): IWhereThan {
				return $this->createThanFragment(__FUNCTION__, $name);
			}

			/**
			 * @inheritdoc
			 */
			public function lte(string $name): IWhereThan {
				return $this->createThanFragment(__FUNCTION__, $name);
			}

			/**
			 * @inheritdoc
			 */
			public function in(string $name): IWhereIn {
				$this->node->setAttribute('type', 'in');
				$this->node->setAttribute('where', $name);
				return new WhereInFragment($this->root, $this->node);
			}

			/**
			 * @inheritdoc
			 */
			public function group(): IWhere {
				$this->node->setAttribute('type', 'group');
				$this->node->addNode($node = new Node('where'));
				return new WhereFragment($this->root, $node);
			}

			/**
			 * @inheritdoc
			 */
			public function and (): IWhere {
				return $this->createRelation(__FUNCTION__);
			}

			/**
			 * @inheritdoc
			 */
			public function or (): IWhere {
				return $this->createRelation(__FUNCTION__);
			}

			protected function createRelation(string $relation): WhereFragment {
				$this->node->setAttribute('relation-to', $relation);
				return new WhereFragment($this->root, $this->node);
			}

			protected function createToFragment(string $type, string $name): IWhereTo {
				$this->node->setAttribute('type', $type);
				$this->node->setAttribute('where', $name);
				return new WhereToFragment($this->root, $this->node);
			}

			protected function createThanFragment(string $type, string $name): IWhereThan {
				$this->node->setAttribute('type', $type);
				$this->node->setAttribute('where', $name);
				return new WhereThanFragment($this->root, $this->node);
			}
		}
