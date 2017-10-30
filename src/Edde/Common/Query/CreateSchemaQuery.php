<?php
	namespace Edde\Common\Query;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Node\Node;

		class CreateSchemaQuery extends AbstractQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;

			public function __construct(ISchema $schema) {
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): INode {
				return (new Node('create-schema', null, [
					'name'     => $this->schema->getName(),
					'relation' => $this->schema->isRelation(),
				]))->addNodeList($this->schema->getNodeList());
			}
		}
