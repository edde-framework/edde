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
		public function testCrate() {
			$crate = new Crate($this->schemaManager->load(FooSchema::class));
		}
	}
