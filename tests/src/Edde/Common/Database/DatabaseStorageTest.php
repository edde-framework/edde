<?php
	namespace Edde\Common\Database;

		use Edde\Api\Storage\Inject\Storage;
		use Edde\Ext\Test\TestCase;

		class DatabaseStorageTest extends TestCase {
			use Storage;

			public function testCreateSchema() {
//				$this->storage->execute(new CreateSchemaQuery());
			}

			public function testSave() {
			}
		}
