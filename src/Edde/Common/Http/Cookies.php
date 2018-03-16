<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

	use Edde\Object;

	/**
	 * Class holding set of cookies.
	 */
	class Cookies extends Object implements \Edde\Http\ICookies {
		/**
		 * @var \Edde\Http\ICookie[]
		 */
		protected $cookies = [];

		/**
		 * @inheritdoc
		 */
		public function add(\Edde\Http\ICookie $cookie) {
			$this->cookies[] = $cookie;
			return $this;
		}
	}
