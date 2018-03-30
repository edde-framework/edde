<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use BarSchema;
	use Edde\Container\Factory\InstanceFactory;
	use Edde\Query\Query;
	use FooBarSchema;
	use FooSchema;

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

		public function testSimpleQuery() {
			$query = new Query();
			$query->uses([
				'foo'     => FooSchema::class,
				'bar'     => BarSchema::class,
				'foo-bar' => FooBarSchema::class,
			]);
			$query->return(['foo', 'bar']);
			$object = $this->storage->toNative($query);
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IStorage::class, MysqlStorage::class), IStorage::class);
		}
	}
