<?php
	declare(strict_types=1);
	namespace Edde\Common\Response;

		use Edde\Api\Content\IContent;
		use Edde\Api\Response\IResponse;
		use Edde\Common\Object\Object;

		class Response extends Object implements IResponse {
			/**
			 * @var IContent
			 */
			protected $content;
			/**
			 * @var int
			 */
			protected $exitCode;

			public function __construct(IContent $content, int $exitCode = 0) {
				$this->content = $content;
				$this->exitCode = $exitCode;
			}

			/**
			 * @inheritdoc
			 */
			public function getContent(): IContent {
				return $this->content;
			}

			/**
			 * @inheritdoc
			 */
			public function setExitCode(int $exitCode): IResponse {
				$this->exitCode = $exitCode;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getExitCode(): int {
				return $this->exitCode;
			}
		}
