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
				$this->setup();
				$this->node->addNode($node = new Node('where', null, ['relation' => 'and']));
				return new WhereFragment($node);
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): INode {
				$this->setup();
				return $this->node;
			}

			protected function handleSetup(): void {
				parent::handleSetup();
				$this->node = new Node('update', $this->schema->getName(), $this->source);
			}
		}
