<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\IQueryBuilder;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Driver\Database\Postgres\PostgresDriver;
		use Edde\Ext\Driver\Database\Postgres\PostgresQueryBuilder;

		class PostgresStorageTest extends AbstractStorageTest {
			public function testPrepareDatabase() {
				$this->storage->query('DROP SCHEMA IF EXISTS "test" CASCADE');
				$this->storage->query('CREATE SCHEMA "test" AUTHORIZATION "edde"');
				$this->assertTrue(true, 'everything is OK!');
			}

			/**
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			protected function setUp() {
				ContainerFactory::inject($this, [
					IDriver::class       => ContainerFactory::instance(PostgresDriver::class, ['pgsql:dbname=edde;user=edde;password=edde;host=172.17.0.1']),
					IQueryBuilder::class => PostgresQueryBuilder::class,
					new ClassFactory(),
				]);
				$this->storage->query('SET search_path TO "test"');
			}
		}
