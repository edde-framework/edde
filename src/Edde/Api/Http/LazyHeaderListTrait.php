<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Lazy request header list dependency.
	 */
	trait LazyHeaderListTrait {
		/**
		 * @var IHeaderList
		 */
		protected $headerList;

		/**
		 * @param IHeaderList $headerList
		 */
		public function lazyHeaderList(IHeaderList $headerList) {
			$this->headerList = $headerList;
		}
	}
