<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\LazyCacheDirectoryTrait;
	use Edde\Api\File\IDirectory;
	use Edde\Common\Cache\AbstractCacheStorage;

	/**
	 * Cache is stored in one file based on a storage namespace.
	 */
	class FlatCacheStorage extends AbstractCacheStorage {
		use LazyCacheDirectoryTrait;
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var IDirectory
		 */
		protected $directory;
		/**
		 * @var array
		 */
		protected $source = [];

		/**
		 * Two flies are sitting on a pile of dog poop. One suggests to the other: “Do you want to hear a really good joke?”
		 *
		 * The other fly replies: “But nothing disgusting like last time, I’m trying to eat here!”
		 *
		 * @param string $namespace
		 */
		public function __construct(string $namespace = '') {
			$this->namespace = $namespace;
		}

		public function save(string $id, $save) {
			$this->use();
			$this->write++;
			$this->source[$id] = $save;
			file_put_contents($this->directory->filename('0.cache'), serialize($this->source));
			return $save;
		}

		public function load($id) {
			$this->use();
			/** @noinspection NotOptimalIfConditionsInspection */
			if (isset($this->source[$id]) || array_key_exists($id, $this->source)) {
				$this->hit++;
				return $this->source[$id];
			}
			$this->miss++;
			return null;
		}

		public function invalidate() {
			$this->use();
			$this->directory->purge();
		}

		protected function prepare() {
			parent::prepare();
			$this->cacheDirectory->create();
			$this->directory = $this->cacheDirectory->directory(sha1($this->namespace))
				->create();
			if (($this->source = @unserialize(($content = file_get_contents($this->directory->filename('0.cache'))) ? $content : '')) === false) {
				$this->source = [];
			}
		}
	}
