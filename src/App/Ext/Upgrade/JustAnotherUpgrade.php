<?php
	namespace App\Ext\Upgrade;

		use Edde\Common\Upgrade\AbstractUpgrade;

		class JustAnotherUpgrade extends AbstractUpgrade {
			public function getVersion(): string {
				return '0.0.0.2';
			}

			public function upgrade(): void {
				sleep(2);
			}
		}
