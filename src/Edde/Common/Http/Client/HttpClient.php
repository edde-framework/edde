<?php
	declare(strict_types=1);

	namespace Edde\Common\Http\Client;

	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Converter\Inject\ConverterManager;
	use Edde\Api\Http\Client\Exception\ClientException;
	use Edde\Api\Http\Client\IHttpClient;
	use Edde\Api\Http\Client\IHttpHandler;
	use Edde\Api\Http\IRequest;
	use Edde\Api\Session\Inject\SessionManager;
	use Edde\Api\Url\IUrl;
	use Edde\Api\Url\UrlException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\Request;
	use Edde\Common\Object\Object;
	use Edde\Common\Url\Url;

	/**
	 * Simple http client implementation.
	 */
	class HttpClient extends Object implements IHttpClient {
		use Container;
		use ConverterManager;
		use SessionManager;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function get($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function post($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function put($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function patch($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function delete($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function head($url): IHttpHandler {
			return $this->request($this->createRequest($url, __FUNCTION__));
		}

		/**
		 * @inheritdoc
		 */
		public function touch($url, string $method = 'HEAD', array $headerList = []): IHttpClient {
			$url = Url::create($url);
			/**
			 * this is a way, how to support sessions by default
			 */
			if ($this->sessionManager->isSession()) {
				$headerList[] = 'Cookie: ' . sprintf('%s=%s', $this->sessionManager->getName(), $this->sessionManager->getSessionId());
			}
			fwrite($handle = stream_socket_client($url->getScheme() . '://' . ($host = $url->getHost()) . ':' . $url->getPort(), $_, $_, 0, STREAM_CLIENT_ASYNC_CONNECT, stream_context_create([
				'ssl' => [
					'verify_peer' => false,
					'allow_self_signed' => true,
					'verify_peer_name' => false,
				],
			])), implode("\r\n", array_merge([
					'HEAD ' . $url->getPath() . ' HTTP/1.1',
					'Host: ' . $host,
				], $headerList)) . "\r\n\r\n");
			fclose($handle);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function request(IRequest $request): IHttpHandler {
			/**
			 * more lines, because some shitty automated code reviewers do not see use of this variable
			 * in return statement (sensiolabs insight)
			 */
			$curl = curl_init((string)$request->getRequestUrl());
			curl_setopt_array($curl, [
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_FAILONERROR => true,
				CURLOPT_FORBID_REUSE => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => 'utf-8',
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_CUSTOMREQUEST => $method = $request->getMethod(),
				CURLOPT_POST => $method === 'POST',
			]);
			return $this->container->create(HttpHandler::class, [
				$request,
				$curl,
			], __METHOD__);
		}

		/**
		 * @inheritdoc
		 * @throws \Edde\Api\Http\Client\Exception\ClientException
		 */
		protected function handleInit() {
			parent::handleInit();
			if (extension_loaded('curl') === false) {
				throw new ClientException('Curl extension is not loaded in PHP.');
			}
		}

		/**
		 * @param IUrl|string $url
		 * @param string      $method
		 *
		 * @return IRequest
		 * @throws UrlException
		 */
		protected function createRequest($url, string $method): IRequest {
			/** @var $request Request */
			$request = $this->container->create(Request::class, [
				RequestUrl::create($url),
				new HeaderList(),
				new CookieList(),
			]);
			$request->setMethod($method);
			return $request;
		}
	}
