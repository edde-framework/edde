<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

		use Edde\Api\Http\ICookie;
		use Edde\Common\Object\Object;

		class Cookie extends Object implements ICookie {
			/**
			 * @var array
			 */
			protected $cookie;

			/**
			 * @param string $name
			 * @param string $value
			 * @param int    $expire
			 * @param string $path
			 * @param string $domain
			 * @param bool   $secure
			 * @param bool   $httpOnly
			 */
			public function __construct(string $name, $value, int $expire = null, string $path = '/', string $domain = null, bool $secure = false, bool $httpOnly = false) {
				$this->cookie = [
					'name'     => $name,
					'value'    => $value,
					'expire'   => $expire,
					'path'     => $path,
					'domain'   => $domain,
					'secure'   => $secure,
					'httpOnly' => $httpOnly,
				];
			}

			/**
			 * @inheritdoc
			 */
			public function getName(): string {
				return (string)$this->cookie['name'];
			}

			/**
			 * @inheritdoc
			 */
			public function getValue() {
				return $this->cookie['value'];
			}

			/**
			 * @inheritdoc
			 */
			public function getExpire(): ?int {
				return $this->cookie['expire'];
			}

			/**
			 * @inheritdoc
			 */
			public function getPath(): string {
				return $this->cookie['path'];
			}

			/**
			 * @inheritdoc
			 */
			public function getDomain(): ?string {
				return $this->cookie['domain'];
			}

			/**
			 * @inheritdoc
			 */
			public function isSecure(): bool {
				return $this->cookie['secure'];
			}

			/**
			 * @inheritdoc
			 */
			public function isHttpOnly(): bool {
				return $this->cookie['httpOnly'];
			}

			/**
			 * @inheritdoc
			 */
			public function toArray(): array {
				return $this->cookie;
			}
		}
