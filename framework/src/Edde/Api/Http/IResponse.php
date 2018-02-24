<?php
	declare(strict_types=1);
	namespace Edde\Api\Http;

	interface IResponse extends IHttp {
		/**
		 * everything is nice and shiny
		 */
		const R200_OK = 200;
		/**
		 * when the thing has been successfully created
		 */
		const R200_OK_CREATED = 201;
		/**
		 * something was requested, but on output is no content
		 */
		const R200_NO_CONTENT = 204;
		/**
		 * when there is a pagination in returned data
		 */
		const R200_PARTIAL_CONTENT = 206;
		const R400_BAD_REQUEST = 400;
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
