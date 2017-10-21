<?php
	namespace Edde\Common\Database;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Database\IDriver;
		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Database\Driver\PostgresDriver;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Test\TestCase;
		use Edde\Test\SimpleSchema;

		class DatabaseStorageTest extends TestCase {
			use SchemaManager;
			use Storage;

			public function testPrepareDatabase() {
				$this->storage->query('DROP SCHEMA IF EXISTS "test" CASCADE');
				$this->storage->query('CREATE SCHEMA "test" AUTHORIZATION "edde"');
				$this->assertTrue(true, 'everything is OK!');
			}

			/**
			 * @throws UnknownSchemaException
			 */
			public function testCreateSchema() {
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema(SimpleSchema::class)));
			}

			public function testSave() {
			}

			/**
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			protected function setUp() {
				ContainerFactory::inject($this, [
					IDriver::class => ContainerFactory::instance(PostgresDriver::class, ['pgsql:dbname=edde;user=edde;password=edde;host=172.17.0.1']),
					new ClassFactory(),
				]);
				$this->storage->query('SET search_path TO "test"');
			}
		}
