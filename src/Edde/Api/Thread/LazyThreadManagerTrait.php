<?php
	declare(strict_types=1);

	namespace Edde\Api\Thread;

	trait LazyThreadManagerTrait {
		/**
		 * @var IThreadManager
		 */
		protected $threadManager;

		/**
		 * @param IThreadManager $threadManager
		 */
		public function lazyThreadManager(IThreadManager $threadManager) {
			$this->threadManager = $threadManager;
		}
	}
