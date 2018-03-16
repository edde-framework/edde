<?php
	declare(strict_types=1);
	namespace Edde\Http;

	interface IResponse extends IHttp {
		const R200_OK = 200;
		const R200_OK_CREATED = 201;
		const R200_NO_CONTENT = 204;
		const R200_PARTIAL_CONTENT = 206;
		const R400_BAD_REQUEST = 400;
		const R400_UNAUTHORIZED = 401;
		const R400_FORBIDDEN = 403;
		const R400_NOT_FOUND = 404;
		const R400_NOT_ALLOWED = 405;
		const R500_SERVER_ERROR = 500;

		/**
		 * set http status code
		 *
		 * @param int $code
		 *
		 * @return IResponse
		 */
		public function setCode(int $code): IResponse;

		/**
		 * get current http status code; defaults to 200
		 *
		 * @return int
		 */
		public function getCode(): int;

		/**
		 * execute this response (that means all the http stuff will be sent)
		 *
		 * @return IResponse
		 */
		public function execute(): IResponse;
	}
