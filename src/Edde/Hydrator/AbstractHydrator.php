<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use Edde\Edde;

	abstract class AbstractHydrator extends Edde implements IHydrator {
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
