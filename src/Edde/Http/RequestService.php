<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Content\Content;
	use Edde\Content\IContent;
	use Edde\Content\InputContent;
	use Edde\Edde;
	use Edde\Url\IUrl;
	use Edde\Url\Url;

	class RequestService extends Edde implements IRequestService {
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
				$this->createHeaders()
			);
			$input = fopen('php://input', 'rb');
			$content = null;
			$headers = $this->request->getHeaders();
			$mime = $headers->get('Content-Type', 'application/octet-stream');
			if (empty($_POST) === false) {
				$content = new Content($_POST, $mime);
			} else if (fgetc($input) !== false) {
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

		protected function createHeaders(): IHeaders {
			$headers = new Headers();
			$mystics = [
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
					if (isset($mystics[$key]) === false || isset($_SERVER[$key]) === false) {
						$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
						$headers->add($key, $value);
					}
				} else if (isset($mystics[$key])) {
					$headers->add($mystics[$key], $value);
				}
			}
			return $headers;
		}
	}
