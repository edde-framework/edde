<?php
	namespace Edde\Common\Http;

		use Edde\Api\Content\IContent;
		use Edde\Api\Http\IContentType;
		use Edde\Api\Http\ICookies;
		use Edde\Api\Http\IHeaders;
		use Edde\Api\Http\IResponse;

		class Response extends AbstractHttp implements IResponse {
			/**
			 * @var int
			 */
			protected $code;

			public function __construct(IContent $content = null, IHeaders $headers = null, ICookies $cookies = null) {
				parent::__construct($headers ?: new Headers(), $cookies ?: new Cookies());
				$this->content = $content;
				$this->code = self::R200_OK;
				$this->setContentType(new ContentType('text/plain', ['charset' => 'utf-8']));
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
			public function setContentType(IContentType $contentType): IResponse {
				$this->headers->setContentType($contentType);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getContentType(): IContentType {
				return $this->headers->getContentType();
			}

			/**
			 * @inheritdoc
			 */
			public function execute(): IResponse {
				http_response_code($this->code);
				foreach ($this->headers as $name => $header) {
					header("$name: $header", false);
				}
				return $this;
			}
		}
