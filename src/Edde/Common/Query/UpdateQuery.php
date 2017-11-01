<?php
	namespace Edde\Common\Query;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\IUpdateQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Node\Node;
		use Edde\Common\Query\Fragment\WhereFragment;

		class UpdateQuery extends InsertQuery implements IUpdateQuery {
			/**
			 * @var INode
			 */
			protected $where;

			public function __construct(ISchema $schema, array $source) {
				parent::__construct($schema, $source);
				$this->type = 'update';
			}

			/**
			 * @inheritdoc
			 */
			public function where(): IWhere {
				if ($this->where === null) {
					$this->where = new Node('where-list');
				}
				$this->where->addNode($node = new Node('where'));
				return new WhereFragment($this->node, $node);
			}

			/**
			 * @inheritdoc
			 */
			public function hasWhere(): bool {
				return $this->where !== null;
			}

			/**
			 * @inheritdoc
			 */
			public function getWhere(): INode {
				return $this->where;
			}
		}
