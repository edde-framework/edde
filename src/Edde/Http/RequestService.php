<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Content\IContent;
	use Edde\Content\InputContent;
	use Edde\Content\PostContent;
	use Edde\Edde;
	use Edde\Service\Http\HttpUtils;
	use Edde\Url\IUrl;
	use Edde\Url\Url;

	class RequestService extends Edde implements IRequestService {
		use HttpUtils;
		/** @var IRequest */
		protected $request;

		/** @inheritdoc */
		public function getRequest(): IRequest {
			if ($this->request) {
				return $this->request;
			}
			$this->request = new Request(
				Url::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']),
				strtoupper($_SERVER['REQUEST_METHOD']),
				$_SERVER['REMOTE_ADDR'] ?? null,
				$this->createHeaders()
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
				$content = new InputContent($mime);
			}
			fclose($input);
			$this->request->setContent($content);
			return $this->request;
		}

		/** @inheritdoc */
		public function getContent(): IContent {
			if (($content = $this->getRequest()->getContent()) === null) {
				throw new EmptyBodyException('Current request has no content.');
			}
			return $content;
		}

		/** @inheritdoc */
		public function getUrl(): IUrl {
			return $this->getRequest()->getUrl();
		}

		/** @inheritdoc */
		public function getMethod(): string {
			return $this->getRequest()->getMethod();
		}

		/** @inheritdoc */
		public function getHeaders(): IHeaders {
			return $this->getRequest()->getHeaders();
		}

		/** @inheritdoc */
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
	}
