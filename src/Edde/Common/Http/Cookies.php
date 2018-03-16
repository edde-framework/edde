<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookie;
	use Edde\Api\Http\ICookies;
	use Edde\Object;

	/**
	 * Class holding set of cookies.
	 */
	class Cookies extends Object implements ICookies {
		/**
		 * @var ICookie[]
		 */
		protected $cookies = [];

		/**
		 * @inheritdoc
		 */
		public function add(ICookie $cookie) {
			$this->cookies[] = $cookie;
			return $this;
		}
	}
