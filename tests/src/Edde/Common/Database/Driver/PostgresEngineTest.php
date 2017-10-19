<?php
	namespace Edde\Common\Database\Driver;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Database\IDriver;
		use Edde\Api\Database\Inject\Driver;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Common\Query\NativeQuery;
		use Edde\Common\Schema\Schema;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Test\TestCase;

		class PostgresEngineTest extends TestCase {
			use Driver;

			/**
			 * @throws DriverQueryException
			 */
			public function testNativeQuery() {
				/**
				 * cleanup public schema by dropping
				 */
				$this->driver->native(new NativeQuery('DROP SCHEMA IF EXISTS "test" CASCADE'));
				$this->driver->native(new NativeQuery('CREATE SCHEMA "test" AUTHORIZATION "edde"'));
				$this->driver->native(new NativeQuery('SET search_path TO "test"'));
				$this->assertTrue(true, 'everything looks nice here!');
			}

			/**
			 * @throws DriverQueryException
			 */
			public function testCreateSchema() {
				$schema = Schema::create('some-cool-schema');
				$schema->primary('guid');
				$schema->string('property-for-this-table')->required();
				$this->driver->execute(new CreateSchemaQuery($schema));
			}

			/**
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			protected function setUp() {
				/**
				 * parent missing intentionally
				 */
				ContainerFactory::inject($this, [
					IDriver::class => ContainerFactory::instance(PostgresDriver::class, ['pgsql:dbname=edde;user=edde;password=edde;host=172.17.0.1']),
					new ClassFactory(),
				]);
			}
		}
