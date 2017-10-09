<?php
	declare(strict_types=1);
	namespace Edde\Common\Router;

		use Edde\Api\Element\IElement;
		use Edde\Api\Router\IRequest;
		use Edde\Api\Router\IResponse;
		use Edde\Common\Object\Object;

		class Request extends Object implements IRequest {
			/**
			 * @var IElement
			 */
			protected $element;
			/**
			 * @var IResponse
			 */
			protected $response;

			public function __construct(IElement $element, IResponse $response) {
				$this->element = $element;
				$this->response = $response;
			}

			/**
			 * @inheritdoc
			 */
			public function getElement(): IElement {
				return $this->element;
			}

			/**
			 * @inheritdoc
			 */
			public function getResponse(): IResponse {
				return $this->response;
			}
		}
