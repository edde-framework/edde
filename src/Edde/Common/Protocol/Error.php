<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	class Error extends Element {
		public function __construct(int $code = 0, string $message = null) {
			parent::__construct('error', null, [
				'code'    => $code,
				'message' => $message,
			]);
		}

		public function setException(string $exception) {
			$this->setAttribute('exception', $exception);
			return $this;
		}
	}
