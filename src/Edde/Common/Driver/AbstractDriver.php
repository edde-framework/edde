<?php
	namespace Edde\Common\Driver;

		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\INativeBatch;
		use Edde\Common\Object\Object;

		abstract class AbstractDriver extends Object implements IDriver {
			/**
			 * @inheritdoc
			 */
			public function batch(INativeBatch $nativeBatch) {
				$last = null;
				foreach ($nativeBatch as $nativeQuery) {
					$last = $this->execute($nativeQuery);
				}
				return $last;
			}
		}
