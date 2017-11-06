<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\IProperty;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ITarget;
		use Edde\Common\Object\Object;

		class Target extends Object implements ITarget {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var IProperty
			 */
			protected $property;

			public function __construct(ISchema $schema, IProperty $property) {
				$this->schema = $schema;
				$this->property = $property;
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(): ISchema {
				return $this->schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getProperty(): IProperty {
				return $this->property;
			}
		}
