<?php
	namespace Edde\Common\Http;

		use Edde\Api\Http\IContentType;
		use Edde\Api\Http\ICookies;
		use Edde\Api\Http\IHeaders;
		use Edde\Api\Http\IRequest;
		use Edde\Api\Http\IRequestService;
		use Edde\Api\Url\IUrl;
		use Edde\Common\Content\InputContent;
		use Edde\Common\Content\PostContent;
		use Edde\Common\Object\Object;
		use Edde\Common\Url\Url;

		class RequestService extends Object implements IRequestService {
			/**
			 * @var IRequest
			 */
			protected $request;

			/**
			 * @inheritdoc
			 */
			public function getRequest(): IRequest {
				if ($this->request) {
					return $this->request;
				}
				$this->request = new Request(Url::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), $this->createHeaders(), $this->createCookies());
				$input = fopen('php://input', 'rb');
				$content = null;
				if (empty($_POST) === false) {
					$content = new PostContent($_POST);
				} else if (fgetc($input) !== false) {
					$headers = $this->request->getHeaders();
					$contentType = $headers->getContentType();
					$content = new InputContent('stream://' . $contentType->getMime());
				}
				fclose($input);
				$this->request->setContent($content);
				return $this->request;
			}

			/**
			 * @inheritdoc
			 */
			public function getUrl(): IUrl {
				return $this->getRequest()->getUrl();
			}

			/**
			 * @inheritdoc
			 */
			public function getMethod(): string {
				return $this->getRequest()->getMethod();
			}

			/**
			 * @inheritdoc
			 */
			public function getHeaders(): IHeaders {
				return $this->getRequest()->getHeaders();
			}

			/**
			 * @inheritdoc
			 */
			public function getContentType():?IContentType {
				return $this->getHeaders()->getContentType();
			}

			protected function createHeaders(): IHeaders {
				$headers = [];
				$mysticList = [
					'CONTENT_TYPE'   => 'Content-Type',
					'CONTENT_LENGTH' => 'Content-Length',
					'CONTENT_MD5'    => 'Content-Md5',
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
				return new Headers($headers);
			}

			protected function createCookies(): ICookies {
				$cookies = new Cookies();
				foreach ($_COOKIE as $name => $value) {
					$cookies->add(new Cookie($name, $value, 0, null, null));
				}
				return $cookies;
			}
		}
