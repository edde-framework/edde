<?php
	declare(strict_types=1);
	namespace Edde\Common\Response;

		use Edde\Api\Element\IElement;
		use Edde\Api\Response\IResponse;
		use Edde\Common\Object\Object;

		class Response extends Object implements IResponse {
			/**
			 * @var IElement
			 */
			protected $element;
			/**
			 * @var int
			 */
			protected $exitCode = 0;

			public function __construct(IElement $element = null) {
				$this->element = $element;
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
