<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookie;
	use Edde\Api\Http\ICookieList;
	use Edde\Common\Object\Object;

	/**
	 * Class holding set of cookies.
	 */
	class CookieList extends Object implements ICookieList {
		/**
		 * @var ICookie[]
		 */
		protected $cookieList = [];

		/**
		 * @inheritdoc
		 */
		public function add(ICookie $cookie) {
			$this->cookieList[] = $cookie;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setupCookieList(): ICookieList {
			foreach ($this->cookieList as $cookie) {
				$cookie->setupCookie();
			}
			return $this;
		}
	}
