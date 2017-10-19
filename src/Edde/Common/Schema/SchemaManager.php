<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ISchemaManager;
		use Edde\Common\Object\Object;

		class SchemaManager extends Object implements ISchemaManager {
			/**
			 * @var ISchema[]
			 */
			protected $schemaList = [];

			/**
			 * @inheritdoc
			 */
			public function registerSchema(ISchema $schema): ISchemaManager {
				$this->schemaList[$schema->getName()] = $schema;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function registerSchemaList(array $schemaList): ISchemaManager {
				foreach ($schemaList as $schema) {
					$this->registerSchema($schema);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(string $name): ISchema {
				if (isset($this->schemaList[$name]) === false) {
					throw new UnknownSchemaException(sprintf('Requested unknown schema [%s].%s', $name, $this->isSetup() ? ' Schema manager has not been set up. Try call setup() method.' : ''));
				}
				return $this->schemaList[$name];
			}
		}
