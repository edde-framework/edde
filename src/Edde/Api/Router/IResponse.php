<?php
	declare(strict_types=1);
	namespace Edde\Api\Router;

	interface IResponse {
		/**
		 * even an application is executed in http mode, it could return
		 * a return code
		 *
		 * @return int
		 */
		public function getCode(): int;
	}
