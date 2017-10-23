<?php
	namespace App\Ext\Upgrade;

		use Edde\Common\Upgrade\AbstractUpgrade;

		class ZeroUpgrade extends AbstractUpgrade {
			/**
			 * @inheritdoc
			 */
			public function getVersion(): string {
				return '0.0.0.0';
			}

			/**
			 * @inheritdoc
			 */
			public function upgrade(): void {
			}
		}
