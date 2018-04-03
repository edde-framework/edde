<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Edde;

	/**
	 * Simple header list implementation over an array.
	 */
	class Headers extends Edde implements IHeaders {
		protected $headers = [];

		/** @inheritdoc */
		public function set(string $name, $value): IHeaders {
			$this->headers[$name] = $value;
			return $this;
		}

		/** @inheritdoc */
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

		/** @inheritdoc */
		public function put(array $headers): IHeaders {
			foreach ($headers as $name => $header) {
				if (is_array($header)) {
					foreach ($header as $v) {
						$this->add($name, $v);
					}
					continue;
				}
				$this->add($name, $header);
			}
			return $this;
		}

		/** @inheritdoc */
		public function has(string $name): bool {
			return isset($this->headers[$name]);
		}

		/** @inheritdoc */
		public function get(string $name, $default = null) {
			return $this->headers[$name] ?? $default;
		}

		/** @inheritdoc */
		public function setContentType(IContentType $contentType): IHeaders {
			$this->set('Content-Type', $contentType);
			return $this;
		}

		/** @inheritdoc */
		public function getContentType(): ?IContentType {
			return $this->get('Content-Type');
		}

		/** @inheritdoc */
		public function getAccepts(): array {
			return $this->get('Accept', []);
		}

		/** @inheritdoc */
		public function toArray(): array {
			return $this->headers;
		}

		/** @inheritdoc */
		public function getIterator() {
			foreach ($this->headers as $name => $value) {
				if (is_array($value)) {
					foreach ($value as $v) {
						yield $name => (string)$v;
					}
					continue;
				}
				yield $name => (string)$value;
			}
		}
	}
