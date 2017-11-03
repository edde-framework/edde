<?php
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Driver\PostgresDriver;

		class PostgresDriverTest extends AbstractDriverTest {
			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testNativeQuery() {
				/**
				 * cleanup public schema by dropping
				 */
				$this->driver->native('DROP SCHEMA IF EXISTS "test" CASCADE');
				$this->driver->native('CREATE SCHEMA "test" AUTHORIZATION "edde"');
				$this->assertTrue(true, 'everything looks nice here!');
			}

			/**
			 * @throws ContainerException
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			protected function setUp() {
				parent::setUp();
				$this->driver->native('set search_path to "test"');
			}

			protected function getDriverFactory() {
				return ContainerFactory::instance(PostgresDriver::class, ['pgsql:dbname=edde;user=edde;password=edde;host=172.17.0.1']);
			}
		}
