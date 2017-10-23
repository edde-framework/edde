<?php
	namespace Edde\Common\Upgrade;

		use Edde\Api\Upgrade\IUpgrade;
		use Edde\Common\Object\Object;

		abstract class AbstractUpgrade extends Object implements IUpgrade {
			/**
			 * @inheritdoc
			 */
			public function rollback(): void {
			}
		}
