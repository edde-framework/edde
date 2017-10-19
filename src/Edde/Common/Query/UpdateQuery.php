<?php
	namespace Edde\Common\Query;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Node\Node;
		use Edde\Common\Query\Fragment\WhereFragment;

		class UpdateQuery extends AbstractQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var array
			 */
			protected $source;
			/**
			 * @var INode
			 */
			protected $node;
			/**
			 * @var WhereFragment
			 */
			protected $whereFragment;

			public function __construct(ISchema $schema, array $source) {
				$this->schema = $schema;
				$this->source = $source;
			}

			public function where(): WhereFragment {
				if ($this->whereFragment) {
					return $this->whereFragment;
				}
				$this->init();
				$this->node->addNode($node = new Node('where'));
				return $this->whereFragment = new WhereFragment($node);
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): INode {
				$this->init();
				return $this->node;
			}

			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node('update', $this->schema->getName(), $this->source);
			}
		}
