<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\Exception\SchemaReflectionException;
		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\Inject\SchemaReflectionService;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ISchemaManager;
		use Edde\Common\Object\Object;

		class SchemaManager extends Object implements ISchemaManager {
			use SchemaReflectionService;
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
					try {
						$this->schemaList[$name] = $this->schemaReflectionService->getSchema($name);
					} catch (SchemaReflectionException $exception) {
						throw new UnknownSchemaException(sprintf('Requested unknown schema [%s].%s', $name, $this->isSetup() ? ' Schema manager has not been set up. Try call setup() method.' : ''), 0, $exception);
					}
				}
				return $this->schemaList[$name];
			}
		}
