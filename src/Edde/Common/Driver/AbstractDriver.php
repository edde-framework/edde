<?php
	namespace Edde\Common\Driver;

		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\INativeTransaction;
		use Edde\Api\Storage\IStream;
		use Edde\Common\Object\Object;

		abstract class AbstractDriver extends Object implements IDriver {
			/**
			 * @inheritdoc
			 */
			public function transaction(INativeTransaction $nativeTransaction): IStream {
				$stream = null;
				foreach ($nativeTransaction as $nativeQuery) {
					$stream = $this->execute($nativeQuery);
				}
				return $stream;
			}
		}
