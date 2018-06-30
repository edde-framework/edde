<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Service\Schema\SchemaLoader;
	use Edde\TestCase;

	class SchemaReflectionLoaderTest extends TestCase {
		use SchemaLoader;

		/**
		 * @throws SchemaException
		 */
		public function testLoadException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Cannot do schema reflection of [nope]: Class nope does not exist');
			$this->schemaLoader->load('nope');
		}
	}
