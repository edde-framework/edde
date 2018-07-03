<?php
	declare(strict_types=1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	/**
	 * Common stuff for cache storage implementation.
	 */
	abstract class AbstractCacheStorage extends Object implements ICacheStorage {
		use ConfigurableTrait;
		/**
		 * how many cache hits was done on this storage
		 *
		 * @var int
		 */
		protected $hit = 0;
		/**
		 * how many cache miss was done on this storage
		 *
		 * @var int
		 */
		protected $miss = 0;
		/**
		 * how many writes was done on this storage
		 *
		 * @var int
		 */
		protected $write = 0;

		/**
		 * @inheritdoc
		 */
		public function getHitCount(): int {
			return $this->hit;
		}

		/**
		 * @inheritdoc
		 */
		public function getMissCount(): int {
			return $this->miss;
		}

		/**
		 * @inheritdoc
		 */
		public function getWriteCount(): int {
			return $this->write;
		}
	}
