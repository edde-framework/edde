<?php
	declare(strict_types=1);
	namespace Edde\Common\Driver;

		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Common\Object\Object;

		abstract class AbstractDriver extends Object implements IDriver {
			/**
			 * @inheritdoc
			 */
			public function transaction(INativeTransaction $nativeTransaction) {
				$stream = null;
				foreach ($nativeTransaction as $nativeQuery) {
					$stream = $this->execute($nativeQuery);
				}
				return $stream;
			}
		}
