<?php
	declare(strict_types=1);
	namespace Edde\Upgrades;

	use Edde\Job\JobSchema;
	use Edde\Upgrade\AbstractUpgrade;

	class JobUpgrade extends AbstractUpgrade {
		/** @inheritdoc */
		public function getVersion(): string {
			return 'job';
		}

		/** @inheritdoc */
		public function upgrade(): void {
			$this->storage->creates([
				JobSchema::class,
			]);
		}
	}
