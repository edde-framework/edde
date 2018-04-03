<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Obj3ct;

	/**
	 * Class holding set of cookies.
	 */
	class Cookies extends Obj3ct implements ICookies {
		/** @var ICookie[] */
		protected $cookies = [];

		/** @inheritdoc */
		public function add(ICookie $cookie) {
			$this->cookies[] = $cookie;
			return $this;
		}
	}
