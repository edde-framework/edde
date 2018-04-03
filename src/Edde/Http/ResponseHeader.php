<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Obj3ct;

	class ResponseHeader extends Obj3ct implements IResponseHeader {
		/** @var array */
		protected $header;

		public function __construct(string $version, int $code, string $message) {
			$this->header = [
				'version' => $version,
				'code'    => $code,
				'message' => $message,
			];
		}

		/** @inheritdoc */
		public function getVersion(): string {
			return (string)$this->header['version'];
		}

		/** @inheritdoc */
		public function getCode(): int {
			return (int)$this->header['code'];
		}

		/** @inheritdoc */
		public function getMessage(): string {
			return (string)$this->header['message'];
		}

		/** @inheritdoc */
		public function toArray(): array {
			return $this->header;
		}

		/** @inheritdoc */
		public function __toString(): string {
			return vsprintf('HTTP/%s %d %s', $this->header);
		}
	}
