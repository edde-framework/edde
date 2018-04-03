<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Edde;

	/**
	 * Class holding set of cookies.
	 */
	class Cookies extends Edde implements ICookies {
		/** @var ICookie[] */
		protected $cookies = [];

		/** @inheritdoc */
		public function add(ICookie $cookie) {
			$this->cookies[] = $cookie;
			return $this;
		}
	}
