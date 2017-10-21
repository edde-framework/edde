<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\IEntity;
		use Edde\Common\Crate\Crate;

		class Entity extends Crate implements IEntity {
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
			public function getSchema(): ISchema {
				return $this->schema;
			}
		}
