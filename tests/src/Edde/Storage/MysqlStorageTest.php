<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Container\Factory\InstanceFactory;
	use Edde\Query\SelectQuery;

	class MysqlStorageTest extends AbstractStorageTest {
		/**
		 * @throws StorageException
		 */
		public function testPrepareDatabase() {
			$this->storage->exec('DROP DATABASE `edde`');
			$this->storage->exec('CREATE DATABASE `edde`');
			$this->storage->exec('USE `edde`');
			$this->assertTrue(true, 'everything looks nice even here!');
		}

		public function testSelectQuery() {
			$selectQuery = new SelectQuery();
			$selectQuery->uses([
			]);
			$selectQuery->join('foo', 'bar', 'foo-bar');
			$selectQuery->returns(['foo', 'bar']);
			$native = $this->storage->toNative($selectQuery);
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IStorage::class, MysqlStorage::class));
		}
	}
