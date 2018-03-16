<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

	use Edde\Api\Http\IResponseHeader;
	use Edde\Object;

	class ResponseHeader extends Object implements IResponseHeader {
		/**
		 * @var array
		 */
		protected $header;

		public function __construct(string $version, int $code, string $message) {
			$this->header = [
				'version' => $version,
				'code'    => $code,
				'message' => $message,
			];
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
		public function getCode(): int {
			return $this->header['code'];
		}

		/**
		 * @inheritdoc
		 */
		public function getMessage(): string {
			return $this->header['message'];
		}

		/**
		 * @inheritdoc
		 */
		public function toArray(): array {
			return $this->header;
		}

		public function __toString(): string {
			return vsprintf('HTTP/%s %d %s', $this->header);
		}
	}
