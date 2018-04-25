<?php
	declare(strict_types=1);
	namespace Edde\Application;

	interface IResponse {
		/**
		 * execute this response (for example send http headers, do some
		 * cool echo of template, ...)
		 *
		 * @return IResponse
		 */
		public function execute(): IResponse;
	}
