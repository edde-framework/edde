<?php
	declare(strict_types=1);

	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\CacheStorageException;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\Cache\AbstractCacheStorage;

	class RedisCacheStorage extends AbstractCacheStorage {
		/**
		 * @var \Redis
		 */
		protected $redis;
		protected $server;

		public function setServer(string $host, int $port): ICacheStorage {
			$this->server = [
				$host,
				$port,
			];
			return $this;
		}

		public function save(string $id, $save) {
			if ($save === null) {
				$this->redis->delete($id);
				return $save;
			}
			if ($this->redis->set($id, serialize($save)) !== true) {
				throw new CacheStorageException(sprintf('Cannot save the given id [%s] to Redis server.', $id));
			}
			return $save;
		}

		public function load($id) {
			if ($this->redis->exists($id) === false) {
				return null;
			}
			return unserialize($this->redis->get($id));
		}

		public function invalidate() {
			$this->redis->flushDB();
		}

		protected function handleSetup() {
			parent::handleSetup();
			if (extension_loaded('redis') === false) {
				throw new CacheStorageException(sprintf("Redis module is not loaded. Yes, I'm telling truth, believe me!"));
			}
			$this->redis = new \Redis();
			call_user_func_array([
				$this->redis,
				'connect',
			], $this->server ?: [
				'127.0.0.1',
				6379,
			]);
			if ($this->redis->select(0) === false) {
				throw new CacheStorageException(sprintf('Cannot select redis database index [%d].', 0));
			}
		}
	}
