<?php
	declare(strict_types = 1);

	namespace Edde\Api\Client;

	use Edde\Api\Event\IEventBus;
	use Edde\Api\File\IFile;
	use Edde\Api\Http\IBody;
	use Edde\Api\Http\IHttpResponse;

	/**
	 * When request is prepared bu a handler, client should create this handler for later execution.
	 */
	interface IHttpHandler extends IEventBus {
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
		 * this should modify an original http request class (if used)
		 *
		 * @param string $name
		 * @param string $value
		 *
		 * @return IHttpHandler
		 */
		public function header(string $name, string $value): IHttpHandler;

		/**
		 * method build round body method (internally should create IBody class)
		 *
		 * @param mixed  $content
		 * @param string $mime
		 * @param string $target
		 *
		 * @return IHttpHandler
		 */
		public function content($content, string $mime = null, string $target = null): IHttpHandler;

		/**
		 * @param IBody $body
		 *
		 * @return IHttpHandler
		 */
		public function body(IBody $body): IHttpHandler;

		/**
		 * if stirng is provided, temp dir will be used
		 *
		 * @param IFile|string $file
		 * @param bool $reset
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
		 * execute a client request
		 *
		 * @return IHttpResponse
		 */
		public function execute(): IHttpResponse;
	}
