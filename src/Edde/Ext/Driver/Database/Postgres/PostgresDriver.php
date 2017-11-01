<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Database\Postgres;

		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Ext\Driver\Database\AbstractDatabaseDriver;

		class PostgresDriver extends AbstractDatabaseDriver {
			protected function exception(\Throwable $throwable): \Throwable {
				if (stripos($message = $throwable->getMessage(), 'unique') !== false) {
					return new DuplicateEntryException($message, 0, $throwable);
				} else if (stripos($message, 'not null') !== false) {
					return new NullValueException($message, 0, $throwable);
				} else if (stripos($message, 'duplicate table') !== false) {
					return new DuplicateTableException($message, 0, $throwable);
				} else if (stripos($message, 'undefined table') !== false) {
					return new UnknownTableException($message, 0, $throwable);
				}
				return new DriverQueryException($message, 0, $throwable);
			}
		}
