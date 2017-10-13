<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

		use Edde\Api\Http\ICookies;
		use Edde\Api\Http\IHeaders;
		use Edde\Api\Http\IResponse;

		class Response extends AbstractHttp implements IResponse {
			/**
			 * @var int
			 */
			protected $code;

			public function __construct(int $code, IHeaders $headers, ICookies $cookies) {
				parent::__construct($headers, $cookies);
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
			public function send(): IResponse {
				http_response_code($this->code);
				$this->headers->send();
				$this->cookies->send();
				if ($this->content) {
					$this->headers->has('Content-Type') ? null : header('Content-Type: ' . $this->content->getType());
					ob_start();
					echo $this->content->getContent();
					header('Content-Length: ' . ob_get_length());
					ob_end_flush();
				}
				return $this;
			}
		}
