<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpRequestFactory;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * Factory for creating IHttpRequests from http input.
	 */
	class HttpRequestFactory extends AbstractDeffered implements IHttpRequestFactory {
		use LazyContainerTrait;

		/**
		 * @inheritdoc
		 */
		public function create() {
			return (new HttpRequest(PostList::create($_POST), $headerList = $this->createHeaderList(), CookieList::create($_COOKIE)))->setRequestUrl(RequestUrl::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']))
				->setMethod($_SERVER['REQUEST_METHOD'] ?? '')
				->setRemoteAddress($_SERVER['REMOTE_ADDR'] ?? '')
				->setRemoteHost($_SERVER['REMOTE_HOST'] ?? '')
				->setBody($this->container->inject(new Body(function () {
					return file_get_contents('php://input');
				}, (string)$headerList->getContentType())));
		}

		/**
		 * @return IHeaderList
		 */
		protected function createHeaderList(): IHeaderList {
			$headers = [];
			$mysticList = [
				'CONTENT_TYPE' => 'Content-Type',
				'CONTENT_LENGTH' => 'Content-Length',
				'CONTENT_MD5' => 'Content-Md5',
			];
			/** @noinspection ForeachSourceInspection */
			foreach ($_SERVER as $key => $value) {
				if (empty($value)) {
					continue;
				}
				if (strpos($key, 'HTTP_') === 0) {
					$key = substr($key, 5);
					if (isset($mysticList[$key]) === false || isset($_SERVER[$key]) === false) {
						$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
						$headers[$key] = $value;
					}
				} else if (isset($mysticList[$key])) {
					$headers[$mysticList[$key]] = $value;
				}
			}
			if (isset($headers['Authorization']) === false) {
				if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
					$headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
				} else if (isset($_SERVER['PHP_AUTH_USER'])) {
					$password = $_SERVER['PHP_AUTH_PW'] ?? '';
					$headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $password);
				} else if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
					$headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
				}
			}
			return (new HeaderList())->put($headers);
		}
	}
