<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookie;
	use Edde\Common\AbstractObject;

	class Cookie extends AbstractObject implements ICookie {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $value;
		/**
		 * @var int
		 */
		protected $expire;
		/**
		 * @var string
		 */
		protected $path;
		/**
		 * @var string
		 */
		protected $domain;
		/**
		 * @var bool
		 */
		protected $secure;
		/**
		 * @var bool
		 */
		protected $httpOnly;

		/**
		 * @param string $name
		 * @param string $value
		 * @param int $expire
		 * @param string $path
		 * @param string $domain
		 * @param bool $secure
		 * @param bool $httpOnly
		 */
		public function __construct($name, $value, $expire, $path, $domain, $secure = false, $httpOnly = false) {
			$this->name = $name;
			$this->value = $value;
			$this->expire = $expire;
			$this->path = $path;
			$this->domain = $domain;
			$this->secure = $secure;
			$this->httpOnly = $httpOnly;
		}

		public function getName() {
			return $this->name;
		}

		public function getValue() {
			return $this->value;
		}

		public function getExpire() {
			return $this->expire;
		}

		public function getPath() {
			return $this->path;
		}

		public function getDomain() {
			return $this->domain;
		}

		public function isSecure() {
			return $this->secure;
		}

		public function isHttpOnly() {
			return $this->httpOnly;
		}
	}
