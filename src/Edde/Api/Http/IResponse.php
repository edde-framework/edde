<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	interface IResponse extends IHttp {
		const R200_OK = 200;
		const R200_OK_CREATED = 201;
		const R400_BAD_REQUEST = 400;
		const R400_NOT_FOUND = 404;
		const R400_NOT_ALLOWED = 405;
		const R500_SERVER_ERROR = 500;

		/**
		 * set the http response code
		 *
		 * @param int $code
		 *
		 * @return IResponse
		 */
		public function setCode(int $code): IResponse;

		/**
		 * return http response code
		 *
		 * @return int
		 */
		public function getCode(): int;

		/**
		 * set a location header
		 *
		 * @param string $redirect
		 *
		 * @return IResponse
		 */
		public function redirect(string $redirect): IResponse;

		/**
		 * send current response
		 *
		 * @return IResponse
		 */
		public function send(): IResponse;
	}
