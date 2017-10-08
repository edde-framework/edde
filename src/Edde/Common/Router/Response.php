<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

	use Edde\Api\Element\IElement;
	use Edde\Api\Router\IRequest;
	use Edde\Api\Router\IResponse;
	use Edde\Common\Object\Object;

	class Response extends Object implements IResponse {
		/**
		 * @var IRequest
		 */
		protected $request;
		/**
		 * @var IElement
		 */
		protected $element;
		/**
		 * @var int
		 */
		protected $code = 0;

		public function __construct(IRequest $request, IElement $element = null) {
			$this->request = $request;
			$this->element = $element;
		}

		/**
		 * @inheritdoc
		 */
		public function getRequest(): IRequest {
			return $this->request;
		}

		/**
		 * @inheritdoc
		 */
		public function getCode(): int {
			return $this->code;
		}
	}
