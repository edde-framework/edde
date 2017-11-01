<?php
	declare(strict_types=1);
	namespace Edde\Common\Converter;

		use Edde\Api\Converter\IConverter;
		use Edde\Common\Object\Object;

		abstract class AbstractConverter extends Object implements IConverter {
			protected $sourceList;
			protected $targetList;

			public function __construct(array $sourceList, array $targetList) {
				$this->sourceList = $sourceList;
				$this->targetList = $targetList;
			}

			/**
			 * @inheritdoc
			 */
			public function getSourceList(): array {
				return $this->sourceList;
			}

			/**
			 * @inheritdoc
			 */
			public function getTargetList(): array {
				return $this->targetList;
			}
		}
