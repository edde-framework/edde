<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Lock\LazyLockDirectoryTrait;
	use Edde\Api\Store\LazyStoreDirectoryTrait;
	use Edde\Api\Store\LazyStoreManagerTrait;
	use Edde\Api\Store\LazyStoreTrait;
	use Edde\Common\Lock\ForeignLockException;
	use Edde\Common\Lock\LockedException;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;
	use Edde\Test\DummyStore;

	require_once __DIR__ . '/../assets/assets.php';

	class FileStoreTest extends TestCase {
		use LazyLockDirectoryTrait;
		use LazyStoreDirectoryTrait;
		use LazyStoreManagerTrait;
		use LazyContainerTrait;
		use LazyStoreTrait;

		public function testLock() {
			$this->lockDirectory->purge();
			$this->storeDirectory->purge();
			$this->expectException(LockedException::class);
			$this->expectExceptionMessage('The name (id) [Edde\Common\Store\StoreManager/foo] is already locked.');
			$this->assertInstanceOf(StoreManager::class, $this->store);
			$this->store->lock('foo');
			$this->store->lock('foo', false);
		}

		public function testRepetativeLock() {
			$this->assertFalse($this->store->isLocked());
			$this->store->lock();
			$this->assertTrue($this->store->isLocked());
			$this->store->unlock();
			$this->assertFalse($this->store->isLocked());
			$this->store->lock();
			$this->assertTrue($this->store->isLocked());
			$this->store->unlock();
			$this->assertFalse($this->store->isLocked());
		}

		public function testThreadLock() {
			$this->store->lock();
			$this->assertTrue($this->store->isLocked());
		}

		public function testUnlockKaboom() {
			$this->expectException(ForeignLockException::class);
			$this->expectExceptionMessage('Lock [Edde\Common\Store\StoreManager] cannot be unlocked because it was created by another lock (or in another thread). Use Edde\Api\Lock\ILock::kill() to kill the lock.');
			$this->store->unlock();
		}

		public function testThreadUnlock() {
			$this->assertTrue($this->store->isLocked());
			$this->store->kill();
			$this->assertFalse($this->store->isLocked());
		}

		public function testSaveData() {
			$this->store->set('foo', 'yapee!');
			self::assertEquals('yapee!', $this->store->get('foo'));
			self::assertEquals('this is default', $this->store->get('moo', 'this is default'));
		}

		public function testThreadedData() {
			self::assertEquals('yapee!', $this->store->get('foo'));
		}

		public function testRestore() {
			self::assertInstanceOf(FileStore::class, $this->storeManager->getCurrentStore());
			$this->storeManager->select(DummyStore::class);
			self::assertInstanceOf(DummyStore::class, $this->storeManager->getCurrentStore());
			$this->storeManager->restore();
			self::assertInstanceOf(FileStore::class, $this->storeManager->getCurrentStore());
		}

		protected function setUp() {
			ContainerFactory::autowire($this);
			$this->storeManager->select(FileStore::class);
			$this->storeManager->registerStore($this->container->create(DummyStore::class));
		}
	}
