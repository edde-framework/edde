<?php
	declare(strict_types = 1);

	namespace Edde\Api\Client;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Event\IEventBus;
	use Edde\Api\Http\IBody;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Url\IUrl;

	interface IHttpClient extends IDeffered, IEventBus {
		/**
		 * do an arbitrary request; the all others are shortcut to this method
		 *
		 * @param IHttpRequest $httpRequest
		 *
		 * @return IHttpHandler
		 */
		public function request(IHttpRequest $httpRequest): IHttpHandler;

		/**
		 * @param string|IUrl $url
		 *
		 * @return IHttpHandler
		 */
		public function get($url): IHttpHandler;

		/**
		 * get & execute & body
		 *
		 * @param string|IUrl $url
		 * @param string $target
		 * @param string|null $mime
		 *
		 * @return mixed
		 */
		public function gete($url, string $target, string $mime = null);

		/**
		 * @param string|IUrl $url target url address
		 *
		 * @return IHttpHandler
		 */
		public function post($url): IHttpHandler;

		/**
		 * post & execute & body
		 *
		 * @param string|IUrl $url
		 * @param IBody $body
		 * @param string $target
		 * @param string|null $mime
		 *
		 * @return mixed
		 */
		public function poste($url, IBody $body = null, string $target, string $mime = null);

		/**
		 * @param string|IUrl $url
		 *
		 * @return IHttpHandler
		 */
		public function put($url): IHttpHandler;

		/**
		 * put & execute & body
		 *
		 * @param string|IUrl $url
		 * @param IBody $body
		 * @param string $target
		 * @param string|null $mime
		 *
		 * @return mixed
		 */
		public function pute($url, IBody $body = null, string $target, string $mime = null);

		/**
		 * @param string|IUrl $url
		 *
		 * @return IHttpHandler
		 */
		public function patch($url): IHttpHandler;

		/**
		 * patch & execute & body
		 *
		 * @param string|IUrl $url
		 * @param IBody $body
		 * @param string $target
		 * @param string|null $mime
		 *
		 * @return mixed
		 */
		public function patche($url, IBody $body = null, string $target, string $mime = null);

		/**
		 * @param string|IUrl $url
		 *
		 * @return IHttpHandler
		 */
		public function delete($url): IHttpHandler;

		/**
		 * delete & execute & body
		 *
		 * @param string|IUrl $url
		 * @param IBody $body
		 * @param string $target
		 * @param string|null $mime
		 *
		 * @return mixed
		 */
		public function deletee($url, IBody $body = null, string $target, string $mime = null);
	}
