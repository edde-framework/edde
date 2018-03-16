<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

	use Edde\Api\Http\IRequestHeader;
	use Edde\Object;

	class RequestHeader extends Object implements IRequestHeader {
		/**
		 * @var string[]
		 */
		protected $header;

		public function __construct(string $method, string $path, string $version) {
			$this->header = [
				'method'  => strtoupper($method),
				'path'    => $path,
				'version' => $version,
			];
		}

		/**
		 * @inheritdoc
		 */
		public function getMethod(): string {
			return $this->header['method'];
		}

		/**
		 * @inheritdoc
		 */
		public function getPath(): string {
			return $this->header['path'];
		}

		/**
		 * @inheritdoc
		 */
		public function getVersion(): string {
			return $this->header['version'];
		}

		/**
		 * @inheritdoc
		 */
		public function toArray(): array {
			return $this->header;
		}

		public function __toString(): string {
			return vsprintf('%s %s HTTP/%s', $this->header);
		}
	}
