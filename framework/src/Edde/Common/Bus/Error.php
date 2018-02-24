<?php
	declare(strict_types=1);
	namespace Edde\Common\Bus;

	use Edde\Api\Bus\IError;

	class Error extends Element implements IError {
		public function __construct(string $message, string $uuid, int $code = null, string $class = null) {
			parent::__construct('error', $uuid, ['message' => $message, 'code' => $code, 'class' => $class]);
		}
	}
