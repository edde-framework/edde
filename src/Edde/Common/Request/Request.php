<?php
	declare(strict_types=1);
	namespace Edde\Common\Request;

		use Edde\Api\Element\IElement;
		use Edde\Api\Request\IRequest;
		use Edde\Common\Object\Object;

		class Request extends Object implements IRequest {
			/**
			 * @var IElement
			 */
			protected $element;
			/**
			 * @var string[]
			 */
			protected $targetList;

			public function __construct(IElement $element, array $targetList = []) {
				$this->element = $element;
				$this->targetList = $targetList;
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
			public function getTargetList(): array {
				return $this->targetList;
			}
		}
