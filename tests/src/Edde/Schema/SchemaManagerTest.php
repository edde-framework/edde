<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Container\ContainerException;
	use Edde\Service\Schema\SchemaManager;
	use Edde\TestCase;
	use Edde\User\UserSchema;
	use ProjectMemberSchema;
	use ProjectSchema;
	use ReflectionException;

	class SchemaManagerTest extends TestCase {
		use SchemaManager;

		/**
		 * @throws SchemaException
		 */
		public function testLoadException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Requested schema [nope] is not loaded; try to use [Edde\Schema\ISchemaManager::load("nope")]');
			$this->schemaManager->getSchema('nope');
		}

		/**
		 * @throws SchemaException
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$this->schemaManager->loads([
				ProjectMemberSchema::class,
				ProjectSchema::class,
				UserSchema::class,
			]);
		}
	}
