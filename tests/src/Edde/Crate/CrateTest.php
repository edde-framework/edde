<?php
	declare(strict_types=1);
	namespace Edde\Crate;

	use Edde\Schema\SchemaException;
	use Edde\Service\Schema\SchemaManager;
	use Edde\TestCase;
	use ProjectSchema;

	class CrateTest extends TestCase {
		use SchemaManager;

		/**
		 * @throws SchemaException
		 */
		public function testPropertyException() {
			$this->schemaManager->load(ProjectSchema::class);
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Requested unknown attribute [ProjectSchema::unknown].');
			$crate = new Crate($this->schemaManager->getSchema(ProjectSchema::class));
			$crate->set('unknown', false);
		}
	}
