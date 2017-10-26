<?php
	namespace Edde\Ext\Driver\Database\Postgres;

		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Ext\Driver\Database\AbstractDatabaseDriver;

		class PostgresDriver extends AbstractDatabaseDriver {
			/**
			 * @param \Throwable $throwable
			 *
			 * @throws \Exception
			 */
			protected function exception(\Throwable $throwable) {
				if (stripos($message = $throwable->getMessage(), 'unique') !== false) {
					throw new DuplicateEntryException($message, 0, $throwable);
				} else if (stripos($message, 'not null') !== false) {
					throw new NullValueException($message, 0, $throwable);
				} else if (stripos($message, 'duplicate table') !== false) {
					throw new DuplicateTableException($message, 0, $throwable);
				} else if (stripos($message, 'undefined table') !== false) {
					throw new UnknownTableException($message, 0, $throwable);
				}
				throw new DriverQueryException($message, 0, $throwable);
			}
		}
