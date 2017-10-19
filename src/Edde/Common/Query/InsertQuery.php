<?php
	namespace Edde\Common\Query;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Node\Node;

		class InsertQuery extends AbstractQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var array
			 */
			protected $source;

			/**
			 * @param ISchema $schema
			 * @param array   $source
			 */
			public function __construct(ISchema $schema, array $source) {
				$this->schema = $schema;
				$this->source = $source;
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): INode {
				return new Node('insert', $this->schema->getName(), $this->source);
			}
		}
