<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * LAzy request cookie list dependency.
	 */
	trait LazyCookieListTrait {
		/**
		 * @var ICookieList
		 */
		protected $cookieList;

		/**
		 * @param ICookieList $cookieList
		 */
		public function lazyCookieList(ICookieList $cookieList) {
			$this->cookieList = $cookieList;
		}
	}
