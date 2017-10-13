<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

		use Edde\Api\Http\IContentType;
		use Edde\Api\Http\IHeaders;
		use Edde\Common\Object\Object;

		/**
		 * Simple header list implementation over an array.
		 */
		class Headers extends Object implements IHeaders {
			protected $headers = [];

			/**
			 * @inheritdoc
			 */
			public function add(string $name, $value): IHeaders {
				/**
				 * more same headers force the original value to became an array with
				 * embedded headers
				 */
				if (isset($this->headers[$name])) {
					if (is_array($this->headers[$name]) === false) {
						$this->headers[$name] = [$this->headers[$name]];
					}
					$this->headers[$name][] = $value;
					return $this;
				}
				$this->headers[$name] = $value;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function has(string $name): bool {
				return isset($this->headers[$name]);
			}

			/**
			 * @inheritdoc
			 */
			public function get(string $name, $default = null) {
				return $this->headers[$name] ?? $default;
			}

			/**
			 * @inheritdoc
			 */
			public function getContentType():?IContentType {
				return $this->get('Content-Type');
			}

			/**
			 * @inheritdoc
			 */
			public function getAcceptList(): array {
				return $this->get('Accept', []);
			}

			/**
			 * @inheritdoc
			 */
			public function toArray(): array {
				return $this->headers;
			}
		}
