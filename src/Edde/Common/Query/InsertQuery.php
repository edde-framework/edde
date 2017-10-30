<?php
	namespace Edde\Common\Query;

		use Edde\Api\Query\IInsertQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Node\Node;

		class InsertQuery extends AbstractQuery implements IInsertQuery {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var array
			 */
			protected $source;
			/**
			 * @var string
			 */
			protected $type;

			public function __construct(ISchema $schema, array $source) {
				$this->schema = $schema;
				$this->source = $source;
				/**
				 * type in constructor is missing intentionally as user is not allowed to change this variable
				 */
				$this->type = 'insert';
			}

			public function handleInit(): void {
				parent::handleInit();
				$this->node = new Node($this->type, $this->source, ['schema' => $this->schema]);
			}
		}
