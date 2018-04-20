<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Container\Factory\InstanceFactory;

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

//		public function testSimpleQuery() {
//			$selectQuery = new SelectQuery();
//			$selectQuery->uses([
//				'user'           => UserSchema::class,
//				'project-member' => ProjectMemberSchema::class,
//				'project'        => ProjectSchema::class,
//			]);
//			$selectQuery->attach('project', 'user', 'project-member');
//			$selectQuery->equalTo('project-member', 'owner', true);
//			$selectQuery->order('project', 'name', 'asc');
//			$selectQuery->return('project');
//			$native = $this->storage->toNative($selectQuery);
//		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IStorage::class, MysqlStorage::class));
		}
	}
