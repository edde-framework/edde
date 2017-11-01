<?php
	namespace Edde\Common\Driver;

		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Common\Object\Object;

		abstract class AbstractDriver extends Object implements IDriver {
			/**
			 * @inheritdoc
			 */
			public function transaction(INativeTransaction $nativeTransaction) {
				$last = null;
				foreach ($nativeTransaction as $nativeQuery) {
					$last = $this->execute($nativeQuery);
				}
				return $last;
			}
		}
