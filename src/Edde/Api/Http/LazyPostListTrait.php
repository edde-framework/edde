<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Request post fields dependency
	 */
	trait LazyPostListTrait {
		/**
		 * @var IPostList
		 */
		protected $postList;

		/**
		 * @param IPostList $postList
		 */
		public function lazyPostList(IPostList $postList) {
			$this->postList = $postList;
		}
	}
