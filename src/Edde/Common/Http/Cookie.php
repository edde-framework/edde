<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookie;
	use Edde\Common\Object\Object;

	class Cookie extends Object implements ICookie {
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
		 * @param int    $expire
		 * @param string $path
		 * @param string $domain
		 * @param bool   $secure
		 * @param bool   $httpOnly
		 */
		public function __construct(string $name, $value, $expire, $path, $domain, $secure = false, $httpOnly = false) {
			$this->name = $name;
			$this->value = $value;
			$this->expire = $expire;
			$this->path = $path;
			$this->domain = $domain;
			$this->secure = $secure;
			$this->httpOnly = $httpOnly;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function getValue() {
			return $this->value;
		}

		/**
		 * @inheritdoc
		 */
		public function getExpire(): int {
			return $this->expire;
		}

		/**
		 * @inheritdoc
		 */
		public function getPath() {
			return $this->path;
		}

		/**
		 * @inheritdoc
		 */
		public function getDomain() {
			return $this->domain;
		}

		/**
		 * @inheritdoc
		 */
		public function isSecure(): bool {
			return $this->secure;
		}

		/**
		 * @inheritdoc
		 */
		public function isHttpOnly(): bool {
			return $this->httpOnly;
		}

		/**
		 * @inheritdoc
		 */
		public function setupCookie(): ICookie {
			setcookie($this->name, $this->value, $this->expire, $this->path, $this->domain, $this->secure, $this->httpOnly);
			return $this;
		}
	}
