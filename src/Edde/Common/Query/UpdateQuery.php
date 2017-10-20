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
				$this->node->getNode('where-list')->addNode($node = new Node('where'));
				return new WhereFragment($this->node, $node);
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): INode {
				$this->init();
				$parameterList = $this->node->getNode('parameter-list');
				$setList = $this->node->getNode('set-list');
				foreach ($this->source as $k => $v) {
					$setList->addNode(new Node('set', null, [
						'column'    => $k,
						'parameter' => $parameterId = (sha1($k . microtime(true) . random_bytes(64))),
					]));
					$parameterList->addNode(new Node('parameter', $v, ['name' => $parameterId]));
				}
				return $this->node;
			}

			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node('update', null, ['table' => $this->schema->getName()]);
			}
		}
