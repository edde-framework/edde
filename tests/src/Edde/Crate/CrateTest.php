<?php
	declare(strict_types=1);
	namespace Edde\Crate;

	use Edde\Schema\SchemaException;
	use Edde\Service\Schema\SchemaManager;
	use Edde\TestCase;
	use FooSchema;

	class CrateTest extends TestCase {
		use SchemaManager;

		/**
		 * @throws SchemaException
		 */
		public function testPropertyException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Requested unknown attribute [FooSchema::unknown].');
			$crate = new Crate($this->schemaManager->load(FooSchema::class));
			$crate->set('unknown', false);
		}
	}
