<?php
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Common\Query\NativeQuery;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Driver\Database\Postgres\PostgresDriver;
		use Edde\Ext\Driver\Database\Postgres\PostgresQueryBuilder;

		class PostgresDriverTest extends AbstractDriverTest {
			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testNativeQuery() {
				/**
				 * cleanup public schema by dropping
				 */
				$this->driver->execute(new NativeQuery('DROP SCHEMA IF EXISTS "test" CASCADE'));
				$this->driver->execute(new NativeQuery('CREATE SCHEMA "test" AUTHORIZATION "edde"'));
				$this->assertTrue(true, 'everything looks nice here!');
			}

			/**
			 * @throws ContainerException
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			protected function setUp() {
				parent::setUp();
				$this->driver->execute(new NativeQuery('SET search_path TO "test"'));
			}

			protected function getDriverFactory() {
				return ContainerFactory::instance(PostgresDriver::class, ['pgsql:dbname=edde;user=edde;password=edde;host=172.17.0.1']);
			}

			protected function getQueryBuilderFactory() {
				return PostgresQueryBuilder::class;
			}
		}
