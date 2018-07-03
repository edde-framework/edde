<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	use Edde\Api\Collection\IList;

	/**
	 * Formal interface for a cookie list implementation.
	 */
	interface ICookieList extends IList {
		/**
		 * set a cookie
		 *
		 * @param ICookie $cookie
		 *
		 * @return $this
		 */
		public function addCookie(ICookie $cookie);
	}
