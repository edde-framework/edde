<?php
	namespace Edde\Common\Query;

		use Edde\Api\Schema\ISchema;
		use Edde\Common\Node\Node;

		class RelationQuery extends AbstractQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;
			protected $source;

			public function __construct(ISchema $schema) {
				$this->schema = $schema;
			}

			protected function handleInit(): void {
				parent::handleInit();
				$this->node = new Node('relation', $this->source, ['schema' => $this->schema]);
			}
		}
