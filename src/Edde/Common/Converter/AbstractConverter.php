<?php
	declare(strict_types=1);
	namespace Edde\Common\Converter;

	use Edde\Converter\IConverter;
	use Edde\Object;

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
		public function getSources(): array {
			return $this->sourceList;
		}

		/**
		 * @inheritdoc
		 */
		public function getTargets(): array {
			return $this->targetList;
		}
	}
