<?php
	declare(strict_types=1);

	namespace Edde\Common\Client;

	use Edde\Api\Client\ClientException;
	use Edde\Api\Client\IHttpHandler;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyTempDirectoryTrait;
	use Edde\Api\Http\IBody;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\IPostList;
	use Edde\Common\AbstractObject;
	use Edde\Common\Client\Event\OnRequestEvent;
	use Edde\Common\Client\Event\RequestDoneEvent;
	use Edde\Common\Client\Event\RequestFailedEvent;
	use Edde\Common\Event\EventTrait;
	use Edde\Common\Http\Body;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\HttpResponse;
	use Edde\Common\Http\HttpUtils;
	use Edde\Common\Strings\StringException;

	/**
	 * Http client handler; this should not be used in common; only as a result from HttpClient calls
	 */
	class HttpHandler extends AbstractObject implements IHttpHandler, ILazyInject {
		use LazyContainerTrait;
		use LazyTempDirectoryTrait;
		use EventTrait;
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;
		/**
		 * @var resource
		 */
		protected $curl;
		/**
		 * cookie file; if set, cookies will be supported
		 *
		 * @var IFile
		 */
		protected $cookie;

		/**
		 * @param IHttpRequest $httpRequest
		 * @param resource     $curl
		 */
		public function __construct(IHttpRequest $httpRequest, $curl) {
			$this->httpRequest = $httpRequest;
			$this->curl = $curl;
		}

		/**
		 * @inheritdoc
		 */
		public function authorization(string $authorization): IHttpHandler {
			$this->header('Authorization', $authorization);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function basic(string $user, string $password): IHttpHandler {
			curl_setopt_array($this->curl, [
				CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
				CURLOPT_USERPWD  => vsprintf('%s:%s', func_get_args()),
			]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function digest(string $user, string $password): IHttpHandler {
			curl_setopt_array($this->curl, [
				CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
				CURLOPT_USERPWD  => vsprintf('%s:%s', func_get_args()),
			]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function header(string $name, string $value): IHttpHandler {
			$this->httpRequest->getHeaderList()
				->set($name, $value);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function keepConnectionAlive(): IHttpHandler {
			$this->header('Connection', 'keep-alive');
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function content($content, string $mime = null, string $target = null): IHttpHandler {
			$content instanceof IPostList ? $this->httpRequest->setPostList($content) : $this->httpRequest->setBody($this->container->inject(new Body($content, $mime, $target)));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function body(IBody $body): IHttpHandler {
			$this->httpRequest->setBody($this->container->inject($body));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function cookie($file, bool $reset = false): IHttpHandler {
			$this->cookie = [
				is_string($file) ? $this->tempDirectory->file($file) : $file,
				$reset,
			];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function agent(string $agent): IHttpHandler {
			curl_setopt($this->curl, CURLOPT_USERAGENT, $agent);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws ClientException
		 * @throws StringException
		 */
		public function execute(): IHttpResponse {
			if ($this->curl === null) {
				throw new ClientException(sprintf('Cannot execute handler for the url [%s] more than once.', (string)$this->httpRequest->getRequestUrl()));
			}
			$options = [];
			if ($body = $this->httpRequest->getBody()) {
				$options[CURLOPT_POSTFIELDS] = $body->convert();
				if (($target = $body->getTarget()) !== null) {
					$this->header('Content-Type', $target);
				}
			}
			$postList = $this->httpRequest->getPostList();
			if ($postList->isEmpty() === false) {
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = $postList->array();
			}
			if ($this->cookie) {
				/** @var $cookie IFile */
				list($cookie, $reset) = $this->cookie;
				$reset ? $cookie->delete() : null;
				$options[CURLOPT_COOKIEFILE] = $options[CURLOPT_COOKIEJAR] = $cookie->getPath();
			}
			$headerList = new HeaderList();
			$cookieList = new CookieList();
			/** @noinspection PhpUnusedParameterInspection */
			/** @noinspection PhpDocSignatureInspection */
			$options[CURLOPT_HEADERFUNCTION] = function ($curl, $header) use ($headerList, $cookieList) {
				$length = strlen($header);
				if (($text = trim($header)) !== '' && strpos($header, ':') !== false) {
					list($header, $content) = explode(':', $header, 2);
					$headerList->set($header, $content = trim($content));
					switch ($header) {
						case 'Set-Cookie':
							$cookieList->addCookie(HttpUtils::cookie($content));
							break;
					}
				}
				return $length;
			};
			$options[CURLOPT_HTTPHEADER] = $this->httpRequest->getHeaderList()
				->headers();
			$options[CURLOPT_FAILONERROR] = false;
			curl_setopt_array($this->curl, $options);
			$this->event($onRequestEvent = new OnRequestEvent($this->httpRequest, $this));
			if ($onRequestEvent->isCanceled()) {
				throw new ClientException(sprintf('%s: request has been canceled', (string)$this->httpRequest->getRequestUrl()));
			}
			$time = microtime(true);
			if (($content = curl_exec($this->curl)) === false) {
				$error = curl_error($this->curl);
				$errorCode = (int)curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
				curl_close($this->curl);
				$this->curl = null;
				$this->event(new RequestFailedEvent($this->httpRequest, $this, microtime(true) - $time));
				throw new ClientException(sprintf('%s: %s', (string)$this->httpRequest->getRequestUrl(), $error), $errorCode);
			}
			$httpCode = (int)curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
			$time = microtime(true) - $time;
			if (is_string($contentType = $headerList->get('Content-Type', curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE)))) {
				$type = HttpUtils::contentType($contentType);
			}
			$headerList->set('Content-Type', $contentType);
			$error = curl_error($this->curl);
			curl_close($this->curl);
			$this->curl = null;
			$this->container->inject($httpResponse = new HttpResponse($this->container->inject(new Body($content, isset($type) ? $type->mime : $contentType))));
			$httpResponse->setHeaderList($headerList);
			$httpResponse->setCookieList($cookieList);
			$this->event(new RequestDoneEvent($this->httpRequest, $this, $httpResponse, $time));
			if ($httpCode >= 400) {
				switch ($httpCode) {
					case 400:
						$exception = BadRequestException::class;
						break;
					case 401:
						$exception = UnauthorizedException::class;
						break;
					case 403:
						$exception = ForbiddenException::class;
						break;
					case 404:
						$exception = NotFoundException::class;
						break;
					case 405:
						$exception = MethodNotAllowedException::class;
						break;
					case 500:
						$exception = ServerErrorException::class;
						break;
					case 503:
						$exception = ServiceUnavailableException::class;
						break;
					default:
						$exception = ClientException::class;
				}
				throw new $exception(sprintf('%s: %s', (string)$this->httpRequest->getRequestUrl(), $error), $httpCode, null, $httpResponse);
			}
			return $httpResponse;
		}
	}
