<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Schema\LazySchemaFactoryTrait;
	use Edde\Api\Schema\SchemaException;
	use Edde\Common\Deffered\AbstractDeffered;

	class SchemaManager extends AbstractDeffered implements ISchemaManager {
		use LazySchemaFactoryTrait;
		/**
		 * @var ISchema[]
		 */
		protected $schemaList = [];

		public function hasSchema(string $schema): bool {
			$this->use();
			return isset($this->schemaList[$schema]);
		}

		public function getSchema(string $schema): ISchema {
			$this->use();
			if (isset($this->schemaList[$schema]) === false) {
				throw new SchemaException(sprintf('Requested unknown schema [%s].', $schema));
			}
			return $this->schemaList[$schema];
		}

		public function getSchemaList(): array {
			$this->use();
			return $this->schemaList;
		}

		protected function prepare() {
			foreach ($this->schemaFactory->create() as $schema) {
				$this->addSchema($schema);
			}
		}

		public function addSchema(ISchema $schema): ISchemaManager {
			$this->schemaList[$schema->getSchemaName()] = $schema;
			return $this;
		}
	}
