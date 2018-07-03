<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IResponse;

	class Response extends AbstractHttp implements IResponse {
		/**
		 * @var int
		 */
		protected $code;

		public function __construct(int $code, IHeaderList $headerList, ICookieList $cookieList) {
			parent::__construct($headerList, $cookieList);
			$this->code = $code;
		}

		/**
		 * @inheritdoc
		 */
		public function setCode(int $code): IResponse {
			$this->code = $code;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getCode(): int {
			return $this->code;
		}

		/**
		 * @inheritdoc
		 */
		public function redirect(string $redirect): IResponse {
			$this->headerList->set('location', $redirect);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function send(): IResponse {
			http_response_code($this->code);
			$this->headerList->setupHeaderList();
			$this->cookieList->setupCookieList();
			if ($this->content) {
				$this->headerList->has('Content-Type') ? null : header('Content-Type: ' . $this->content->getMime());
				ob_start();
				echo $this->content->getContent();
				header('Content-Length: ' . ob_get_length());
				ob_end_flush();
			}
			return $this;
		}
	}
