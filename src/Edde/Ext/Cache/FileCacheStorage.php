<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\CacheStorageException;
	use Edde\Api\Cache\LazyCacheDirectoryTrait;
	use Edde\Common\Cache\AbstractCacheStorage;

	class FileCacheStorage extends AbstractCacheStorage {
		use LazyCacheDirectoryTrait;
		/**
		 * @var string
		 */
		protected $namespace;

		public function __construct(string $namespace = null) {
			$this->namespace = $namespace;
		}

		public function save(string $id, $save) {
			$this->use();
			$file = $this->file($id);
			if ($save === null) {
				/** @noinspection PhpUsageOfSilenceOperatorInspection */
				if (@unlink($file) === false) {
					throw new CacheStorageException(sprintf('Cannot remove cached file [%s] for cache id [%s] from folder [%s].', $file, $id, $this->cacheDirectory));
				}
				return $save;
			}
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			if (($handle = @fopen($file, 'c+b')) === false) {
				throw new CacheStorageException(sprintf('Cannot write to the cache file [%s]. Please check cache folder [%s] permissions.', $file, $this->cacheDirectory));
			}
			ftruncate($handle, 0);
			fwrite($handle, serialize($save));
			fclose($handle);
			$this->write++;
			return $save;
		}

		protected function file($id) {
			return sprintf('%s/%s', $this->cacheDirectory, sha1($this->namespace . $id));
		}

		public function load($id) {
			$this->use();
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			if (($handle = @fopen($this->file($id), 'r+b')) === false) {
				$this->miss++;
				return null;
			}
			$source = unserialize(stream_get_contents($handle));
			fclose($handle);
			$this->hit++;
			return $source;
		}

		public function invalidate() {
			$this->use();
			$this->cacheDirectory->purge();
		}

		protected function prepare() {
			parent::prepare();
			$this->cacheDirectory->create();
		}
	}
