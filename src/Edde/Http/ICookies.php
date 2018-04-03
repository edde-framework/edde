<?php
	declare(strict_types=1);
	namespace Edde\Http;

	/**
	 * Formal interface for a cookie list implementation.
	 */
	interface ICookies {
		/**
		 * set a cookie
		 *
		 * @param ICookie $cookie
		 *
		 * @return $this
		 */
		public function add(ICookie $cookie);
	}
