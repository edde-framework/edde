<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpService;
	use Edde\Api\Http\IRequest;
	use Edde\Api\Http\IResponse;
	use Edde\Common\Converter\Content;
	use Edde\Common\Object\Object;
	use Edde\Common\Url\Url;

	class HttpService extends Object implements IHttpService {
		/**
		 * @var IRequest
		 */
		protected $request;
		/**
		 * @var IResponse
		 */
		protected $response;

		/**
		 * @inheritdoc
		 */
		public function createRequest(): IRequest {
			if ($this->request) {
				return $this->request;
			}
			$this->request = new Request(Url::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), $this->createHeaderList(), $this->createCookieList());
			$input = fopen('php://input', 'r');
			$content = null;
			if (empty($_POST) === false) {
				$content = new Content($_POST, 'post');
			} else if (fgetc($input) !== false) {
				$headerList = $this->request->getHeaderList();
				$contentType = $headerList->getContentType();
				$content = new Content('php://input', 'stream+' . $contentType->getMime());
			}
			fclose($input);
			$this->request->setContent($content);
			return $this->request;
		}

		/**
		 * create header list from incoming request
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
			return new HeaderList($headers);
		}

		protected function createCookieList(): ICookieList {
			$cookieList = new CookieList();
			foreach ($_COOKIE as $name => $value) {
				$cookieList->add(new Cookie($name, $value, 0, null, null));
			}
			return $cookieList;
		}

		/**
		 * @inheritdoc
		 */
		public function createResponse(): IResponse {
			if ($this->response) {
				return $this->response;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function setResponse(IResponse $response): IHttpService {
			$this->response = $response;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function send(): IHttpService {
			$response = $this->createResponse();
			$response->send();
			return $this;
		}
	}
