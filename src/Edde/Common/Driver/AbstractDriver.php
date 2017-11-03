<?php
	declare(strict_types=1);
	namespace Edde\Common\Driver;

		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Object\Object;

		abstract class AbstractDriver extends Object implements IDriver {
			/**
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
			}

			protected function handleSetup(): void {
				parent::handleSetup();
			}
		}
