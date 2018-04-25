<?php
	declare(strict_types=1);
	namespace Edde\Application;

	interface IResponse {
		/**
		 * set http code of this response
		 *
		 * @param int $code
		 *
		 * @return IResponse
		 */
		public function http(int $code): IResponse;

		/**
		 * a bit confusing - set an exit code (even in http mode, application
		 * is ending with method exit())
		 *
		 * @param int $code
		 *
		 * @return IResponse
		 */
		public function exit(int $code): IResponse;

		/**
		 * execute this response (for example send http headers, do some
		 * cool echo of template, ...)
		 *
		 * void as a return type is intentional to prevent confusion; response
		 * should be immutable, mainly after execute()
		 */
		public function execute(): void;
	}
