<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\HttpMessageException;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpMessage;
	use Edde\Common\Deffered\AbstractDeffered;

	class HttpMessage extends AbstractDeffered implements IHttpMessage {
		/**
		 * @var string
		 */
		protected $message;
		/**
		 * @var string
		 */
		protected $headers;
		/**
		 * @var IHeaderList
		 */
		protected $headerList;
		/**
		 * @var IHttpMessage[]
		 */
		protected $messageList = [];

		/**
		 * HttpMessage constructor.
		 *
		 * @param string $message
		 * @param string $headers
		 */
		public function __construct(string $message, string $headers) {
			$this->message = $message;
			$this->headers = $headers;
		}

		/**
		 * @inheritdoc
		 */
		public function getHeaderList(): IHeaderList {
			$this->use();
			return $this->headerList;
		}

		/**
		 * @inheritdoc
		 */
		public function getContentType(string $default = ''): string {
			$this->use();
			return $this->headerList->getContentType()
				->getMime($default);
		}

		/**
		 * @inheritdoc
		 */
		public function getContentList(): array {
			return array_keys($this->messageList);
		}

		/**
		 * @inheritdoc
		 */
		public function getMessageList(): array {
			return $this->messageList;
		}

		/**
		 * @inheritdoc
		 * @throws HttpMessageException
		 */
		public function getMessage(string $contentId): IHttpMessage {
			if (isset($this->messageList[$contentId]) === false) {
				throw new HttpMessageException(sprintf('Requested unknown content id [%s] in http message.', $contentId));
			}
			return $this->messageList[$contentId];
		}

		/**
		 * @inheritdoc
		 */
		public function getBody(): string {
			return $this->message;
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->headerList = new HeaderList();
			$this->headerList->put(HttpUtils::headerList($this->headers, false));
			$contentType = $this->headerList->getContentType();
			if ($contentType->has('boundary')) {
				foreach (array_slice(explode('--' . $contentType->get('boundary'), $this->message), 1, -1) as $boundary) {
					list($headers, $message) = explode("\r\n\r\n", $boundary);
					$message = new self($message, $headers);
					if ($contentId = $message->getContentId()) {
						$this->messageList[$contentId] = $message;
					}
				}
			}
		}

		/**
		 * @inheritdoc
		 */
		public function getContentId() {
			$this->use();
			return trim($this->headerList->get('Content-ID'), '<>');
		}
	}
