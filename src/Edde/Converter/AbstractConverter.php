<?php
	declare(strict_types=1);
	namespace Edde\Converter;

	use Edde\Edde;

	abstract class AbstractConverter extends Edde implements IConverter {
		protected $sources;
		protected $targets;

		public function __construct(array $sources, array $targets) {
			$this->sources = $sources;
			$this->targets = $targets;
		}

		/**
		 * @inheritdoc
		 */
		public function getSources(): array {
			return $this->sources;
		}

		/**
		 * @inheritdoc
		 */
		public function getTargets(): array {
			return $this->targets;
		}
	}
