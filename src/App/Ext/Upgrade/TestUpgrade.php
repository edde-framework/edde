<?php
	namespace App\Ext\Upgrade;

		use Edde\Common\Upgrade\AbstractUpgrade;

		class TestUpgrade extends AbstractUpgrade {
			public function getVersion(): string {
				return '0.0.0.1';
			}

			public function upgrade(): void {
				sleep(3);
			}
		}
