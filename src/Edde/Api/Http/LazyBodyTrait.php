<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Lazy request body dependency.
	 */
	trait LazyBodyTrait {
		/**
		 * @var IBody
		 */
		protected $body;

		/**
		 * @param IBody $body
		 */
		public function lazyBody(IBody $body) {
			$this->body = $body;
		}
	}
