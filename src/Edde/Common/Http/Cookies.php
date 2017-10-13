<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

		use Edde\Api\Http\ICookie;
		use Edde\Api\Http\ICookies;
		use Edde\Common\Object\Object;

		/**
		 * Class holding set of cookies.
		 */
		class Cookies extends Object implements ICookies {
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
			public function send(): ICookies {
				foreach ($this->cookieList as $cookie) {
					$cookie->send();
				}
				return $this;
			}
		}
