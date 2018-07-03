<?php
	declare(strict_types=1);

	namespace Edde\Api\Http\Client;

	use Edde\Api\Converter\IContent;
	use Edde\Api\File\IFile;
	use Edde\Api\Http\IResponse;

	/**
	 * When request is prepared bu a handler, client should create this handler for later execution.
	 */
	interface IHttpHandler {
		/**
		 * @param string $authorization
		 *
		 * @return IHttpHandler
		 */
		public function authorization(string $authorization): IHttpHandler;

		/**
		 * basic auth
		 *
		 * @param string $user
		 * @param string $password
		 *
		 * @return IHttpHandler
		 */
		public function basic(string $user, string $password): IHttpHandler;

		/**
		 * use digest type of authorization
		 *
		 * @param string $user
		 * @param string $password
		 *
		 * @return IHttpHandler
		 */
		public function digest(string $user, string $password): IHttpHandler;

		/**
		 * @return IHttpHandler
		 */
		public function keepConnectionAlive(): IHttpHandler;

		/**
		 * @param array $targetList
		 *
		 * @return IHttpHandler
		 */
		public function setTargetList(array $targetList = null): IHttpHandler;

		/**
		 * this should modify an original http request class (if used)
		 *
		 * @param string $name
		 * @param string $value
		 *
		 * @return IHttpHandler
		 */
		public function header(string $name, string $value): IHttpHandler;

		/**
		 * @param array $headers
		 *
		 * @return IHttpHandler
		 */
		public function headers(array $headers): IHttpHandler;

		/**
		 * if stirng is provided, temp dir will be used
		 *
		 * @param IFile|string $file
		 * @param bool         $reset
		 *
		 * @return IHttpHandler|mixed
		 */
		public function cookie($file, bool $reset = false): IHttpHandler;

		/**
		 * set user agent for this request
		 *
		 * @param string $agent
		 *
		 * @return IHttpHandler
		 */
		public function agent(string $agent): IHttpHandler;

		/**
		 * set the content of the request and target list; selected mime type will be sent as header
		 *
		 * @param IContent $content
		 * @param array    $targetList
		 *
		 * @return IHttpHandler
		 */
		public function content(IContent $content, array $targetList = null): IHttpHandler;

		/**
		 * @param mixed  $payload
		 * @param string $mime
		 *
		 * @return IHttpHandler
		 */
		public function payload($payload, string $mime): IHttpHandler;

		/**
		 * this methods basically sets url encoded data to the request body
		 *
		 * @param array $post
		 *
		 * @return IHttpHandler
		 */
		public function post(array $post): IHttpHandler;

		/**
		 * explicitly set content type of the request
		 *
		 * @param string $contentType
		 *
		 * @return IHttpHandler
		 */
		public function contentType(string $contentType): IHttpHandler;

		/**
		 * execute a client request
		 *
		 * @return IResponse
		 */
		public function execute(): IResponse;
	}
