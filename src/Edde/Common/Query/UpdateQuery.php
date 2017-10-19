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

			public function __construct(ISchema $schema, array $source) {
				$this->schema = $schema;
				$this->source = $source;
			}

			public function where(): WhereFragment {
				$this->init();
				$this->node->addNode($node = new Node('where'));
				return new WhereFragment($node);
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
