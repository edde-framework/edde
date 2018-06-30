<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use function reset;

	class SingleHydrator extends AbstractHydrator {
		/** @inheritdoc */
		public function hydrate(array $source) {
			return reset($source);
		}

		/** @inheritdoc */
		public function input(string $name, array $input): array {
			return $input;
		}

		/** @inheritdoc */
		public function update(string $name, array $update): array {
			return $update;
		}

		/** @inheritdoc */
		public function output(string $name, array $output): array {
			return $output;
		}
	}
