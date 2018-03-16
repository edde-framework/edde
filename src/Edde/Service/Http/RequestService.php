<?php
	declare(strict_types=1);
	namespace Edde\Service\Http;

	use Edde\Api\Http\IContentType;
	use Edde\Api\Http\ICookies;
	use Edde\Api\Http\IHeaders;
	use Edde\Api\Http\IRequest;
	use Edde\Api\Http\IRequestService;
	use Edde\Common\Http\Cookie;
	use Edde\Common\Http\Cookies;
	use Edde\Common\Http\Request;
	use Edde\Common\Url\Url;
	use Edde\Content\InputContent;
	use Edde\Content\PostContent;
	use Edde\Exception\Http\EmptyBodyException;
	use Edde\Exception\Http\NoHttpException;
	use Edde\Exception\Url\UrlException;
	use Edde\Inject\Converter\ConverterManager;
	use Edde\Inject\Http\HttpUtils;
	use Edde\Object;
	use Edde\Url\IUrl;

	class RequestService extends Object implements IRequestService {
		use HttpUtils;
		use ConverterManager;
		/** @var IRequest */
		protected $request;

		/**
		 * @inheritdoc
		 *
		 * @throws UrlException
		 */
		public function getRequest(): IRequest {
			if ($this->request) {
				return $this->request;
			}
			$this->request = new Request(
				Url::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']),
				$this->createHeaders(),
				$this->createCookies()
			);
			$input = fopen('php://input', 'rb');
			$content = null;
			if (empty($_POST) === false) {
				$content = new PostContent($_POST);
			} else if (fgetc($input) !== false) {
				$headers = $this->request->getHeaders();
				$mime = 'application/octet-stream';
				if ($headers->has('Content-Type')) {
					$contentType = $headers->getContentType();
					$mime = $contentType->getMime();
				}
				$content = new InputContent('stream://' . $mime);
			}
			fclose($input);
			$this->request->setContent($content);
			return $this->request;
		}

		/**
		 * @inheritdoc
		 *
		 * @throws \Edde\Exception\Http\EmptyBodyException
		 * @throws \Edde\Exception\Url\UrlException
		 * @throws \Edde\Exception\Http\NoHttpException
		 * @throws \Edde\Converter\ConverterException
		 */
		public function getContent(...$targetList) {
			if (($content = $this->getRequest()->getContent()) === null) {
				throw new EmptyBodyException('Current request has no content.');
			}
			return $this->converterManager->convert($content, $targetList)->getContent();
		}

		/**
		 * @inheritdoc
		 *
		 * @throws NoHttpException
		 * @throws \Edde\Exception\Url\UrlException
		 */
		public function getUrl(): IUrl {
			return $this->getRequest()->getUrl();
		}

		/**
		 * @inheritdoc
		 *
		 * @throws \Edde\Exception\Http\NoHttpException
		 * @throws \Edde\Exception\Url\UrlException
		 */
		public function getMethod(): string {
			return $this->getRequest()->getMethod();
		}

		/**
		 * @inheritdoc
		 *
		 * @throws \Edde\Exception\Http\NoHttpException
		 * @throws \Edde\Exception\Url\UrlException
		 */
		public function getHeaders(): IHeaders {
			return $this->getRequest()->getHeaders();
		}

		/**
		 * @inheritdoc
		 *
		 * @throws \Edde\Exception\Http\NoHttpException
		 * @throws UrlException
		 */
		public function getContentType(): ?IContentType {
			return $this->getHeaders()->getContentType();
		}

		protected function createHeaders(): IHeaders {
			$headers = [];
			$mysticList = [
				'CONTENT_TYPE'   => 'Content-Type',
				'CONTENT_LENGTH' => 'Content-Length',
				'CONTENT_MD5'    => 'Content-Md5',
			];
			foreach ($_SERVER as $key => $value) {
				if (empty($value)) {
					continue;
				}
				if (strpos($key, 'HTTP_') === 0) {
					$key = substr($key, 5);
					if (isset($mysticList[$key]) === false || isset($_SERVER[$key]) === false) {
						$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
						$headers[] = $key . ':' . $value;
					}
				} else if (isset($mysticList[$key])) {
					$headers[] = $mysticList[$key] . ':' . $value;
				}
			}
			return $this->httpUtils->headers($headers);
		}

		protected function createCookies(): ICookies {
			$cookies = new Cookies();
			foreach ($_COOKIE as $name => $value) {
				$cookies->add(new Cookie($name, $value));
			}
			return $cookies;
		}
	}
