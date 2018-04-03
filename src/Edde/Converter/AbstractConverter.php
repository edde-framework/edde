<?php
	declare(strict_types=1);
	namespace Edde\Converter;

	use Edde\Obj3ct;

	abstract class AbstractConverter extends Obj3ct implements IConverter {
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
