<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\IRelation;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ITarget;

		class Relation extends Link implements IRelation {
			/**
			 * @var ISchema
			 */
			protected $schema;

			public function __construct(ISchema $schema, ITarget $from, ITarget $to) {
				parent::__construct($from, $to);
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(): ISchema {
				return $this->schema;
			}
		}
