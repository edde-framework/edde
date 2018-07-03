<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	/**
	 * Simple interface for working with http response as a service.
	 */
	interface IHttpResponse extends IResponse {
		/**
		 * execute response "rendering"; basically it "echoes" output
		 *
		 * @return IHttpResponse
		 */
		public function send(): IHttpResponse;
	}
