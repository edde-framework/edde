<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	use Edde\Api\Deffered\IDeffered;

	/**
	 * Response manager holds current Response (to keep responses immutable).
	 */
	interface IResponseManager extends IDeffered {
		/**
		 * set the current response
		 *
		 * @param IResponse $response
		 *
		 * @return IResponseManager
		 */
		public function response(IResponse $response): IResponseManager;

		/**
		 * if a response is not set, internal default should be applied or empty response should be returned
		 *
		 * @param string $mime
		 *
		 * @return IResponseManager
		 */
		public function setMime(string $mime): IResponseManager;

		/**
		 * return target mime type of request (it can be echoing, json_encoding, ...)
		 *
		 * @return string
		 */
		public function getMime(): string;

		/**
		 * execute response
		 *
		 * @return mixed
		 */
		public function execute();
	}
