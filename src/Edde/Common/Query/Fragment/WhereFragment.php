<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\Fragment\IWhereFragment;
		use Edde\Api\Query\Fragment\IWhereTo;
		use Edde\Common\Node\Node;

		class WhereFragment extends AbstractFragment implements IWhereFragment {
			/**
			 * @var ISchemaFragment
			 */
			protected $schemaFragment;
			/**
			 * @var IWhereFragment[]
			 */
			protected $whereList = [];

			public function __construct(ISchemaFragment $schemaFragment) {
				$this->schemaFragment = $schemaFragment;
			}

			/**
			 * @inheritdoc
			 */
			public function eq(string $name): IWhereTo {
				return $this->whereList[] = new WhereToFragment($this->schemaFragment, __FUNCTION__, $name);
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
		}
