<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\Fragment\IWhereFragment;
		use Edde\Api\Query\Fragment\IWhereIn;
		use Edde\Api\Query\Fragment\IWhereThan;
		use Edde\Api\Query\Fragment\IWhereTo;
		use Edde\Common\Node\Node;

		class WhereFragment extends AbstractFragment implements IWhereFragment {
			/**
			 * @var ISchemaFragment
			 */
			protected $schemaFragment;

			public function __construct(ISchemaFragment $schemaFragment) {
				$this->schemaFragment = $schemaFragment;
			}

			/**
			 * @inheritdoc
			 */
			public function eq(string $name, string $prefix = null): IWhereTo {
				return $this->createToFragment(__FUNCTION__, $name, $prefix);
			}

			/**
			 * @inheritdoc
			 */
			public function neq(string $name, string $prefix = null): IWhereTo {
				return $this->createToFragment(__FUNCTION__, $name, $prefix);
			}

			/**
			 * @inheritdoc
			 */
			public function gt(string $name, string $prefix = null): IWhereThan {
				return $this->createThanFragment(__FUNCTION__, $name, $prefix);
			}

			/**
			 * @inheritdoc
			 */
			public function gte(string $name, string $prefix = null): IWhereThan {
				return $this->createThanFragment(__FUNCTION__, $name, $prefix);
			}

			/**
			 * @inheritdoc
			 */
			public function lt(string $name, string $prefix = null): IWhereThan {
				return $this->createThanFragment(__FUNCTION__, $name, $prefix);
			}

			/**
			 * @inheritdoc
			 */
			public function lte(string $name, string $prefix = null): IWhereThan {
				return $this->createThanFragment(__FUNCTION__, $name, $prefix);
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
			public function group(): IWhereFragment {
				$this->node->setAttribute('type', 'group');
				$this->node->addNode($node = new Node('where'));
				return new WhereFragment($this->root, $node);
			}

			/**
			 * @inheritdoc
			 */
			public function and (): IWhereFragment {
				return $this->createRelation(__FUNCTION__);
			}

			/**
			 * @inheritdoc
			 */
			public function or (): IWhereFragment {
				return $this->createRelation(__FUNCTION__);
			}

			protected function createRelation(string $relation): WhereFragment {
				$this->node->setAttribute('relation-to', $relation);
				return new WhereFragment($this->root, $this->node);
			}

			protected function createToFragment(string $type, string $name, string $prefix = null): IWhereTo {
				$this->node->setAttribute('type', $type);
				$this->node->setAttribute('where', $name);
				$this->node->setAttribute('prefix', $prefix);
				return new WhereToFragment($this->root, $this->node);
			}

			protected function createThanFragment(string $type, string $name, string $prefix = null): IWhereThan {
				$this->node->setAttribute('type', $type);
				$this->node->setAttribute('where', $name);
				$this->node->setAttribute('prefix', $prefix);
				return new WhereThanFragment($this->root, $this->node);
			}
		}
