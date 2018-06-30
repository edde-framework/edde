<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\TestCase;

	class AttributeTest extends TestCase {
		/**
		 * @throws SchemaException
		 */
		public function testGetSchemaException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Property [foo] does not have a reference to schema.');
			$attribute = new Attribute((object)['name' => 'foo']);
			$attribute->getSchema();
		}
	}
