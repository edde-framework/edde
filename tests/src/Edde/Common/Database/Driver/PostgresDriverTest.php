<?php
	namespace Edde\Common\Database\Driver;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Common\Query\NativeQuery;
		use Edde\Ext\Container\ContainerFactory;

		class PostgresDriverTest extends AbstractDriverTest {
			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testNativeQuery() {
				/**
				 * cleanup public schema by dropping
				 */
				$this->driver->native(new NativeQuery('DROP SCHEMA IF EXISTS "test" CASCADE'));
				$this->driver->native(new NativeQuery('CREATE SCHEMA "test" AUTHORIZATION "edde"'));
				$this->assertTrue(true, 'everything looks nice here!');
			}

			/**
			 * @throws ContainerException
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			protected function setUp() {
				parent::setUp();
				$this->driver->native(new NativeQuery('SET search_path TO "test"'));
			}

			protected function getDriverFactory() {
				return ContainerFactory::instance(\Edde\Ext\Driver\Database\Postgres\PostgresDriver::class, ['pgsql:dbname=edde;user=edde;password=edde;host=172.17.0.1']);
			}
		}
