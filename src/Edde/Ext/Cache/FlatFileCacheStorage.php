<?php
	declare(strict_types=1);

	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\LazyCacheDirectoryTrait;
	use Edde\Api\File\IDirectory;

	/**
	 * Cache is stored in one file based on a storage namespace.
	 */
	class FlatFileCacheStorage extends InMemoryCacheStorage {
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
		 * Two flies are sitting on a pile of dog poop. One suggests to the other: “Do you want to hear a really good joke?”
		 *
		 * The other fly replies: “But nothing disgusting like last time, I’m trying to eat here!”
		 *
		 * @param string $namespace
		 */
		public function __construct(string $namespace = '') {
			$this->namespace = $namespace;
		}

		/**
		 * @inheritdoc
		 */
		public function invalidate() {
			$this->directory->purge();
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			parent::handleInit();
			$this->cacheDirectory->create();
			$this->directory = $this->cacheDirectory->directory(sha1($this->namespace))->create();
			if (($this->storage = @unserialize(($content = file_get_contents($this->directory->filename('0.cache'))) ? $content : '')) === false) {
				$this->storage = [];
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function handleSetup() {
			parent::handleSetup();
			register_shutdown_function(function () {
				file_put_contents($this->directory->filename('0.cache'), serialize($this->storage));
			});
		}
	}
