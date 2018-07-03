<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Service providing access to request and response in http context. If used
	 * in cli mode, it should throw an exception.
	 *
	 * This class should maintain current http state.
	 */
	interface IHttpService extends IConfigurable {
		/**
		 * create http request; should be singleton
		 *
		 * @return IRequest
		 */
		public function createRequest(): IRequest;

		/**
		 * create http response; should be singleton
		 *
		 * @return IResponse
		 */
		public function createResponse(): IResponse;

		/**
		 * override current http response
		 *
		 * @param IResponse $response
		 *
		 * @return IHttpService
		 */
		public function setResponse(IResponse $response): IHttpService;

		/**
		 * send current http response
		 *
		 * @return IHttpService
		 */
		public function send(): IHttpService;
	}
