<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver\Database\Mysql;

		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Ext\Driver\Database\AbstractDatabaseDriver;

		class MysqlDriver extends AbstractDatabaseDriver {
			/**
			 * @inheritdoc
			 */
			protected function exception(\Throwable $throwable): \Throwable {
				if (stripos($message = $throwable->getMessage(), 'duplicate') !== false) {
					return new DuplicateEntryException($message, 0, $throwable);
				} else if (stripos($message, 'cannot be null') !== false || stripos($message, 'have a default value') !== false) {
					return new NullValueException($message, 0, $throwable);
				} else if (stripos($message, 'table or view already exists') !== false) {
					return new DuplicateTableException($message, 0, $throwable);
				} else if (stripos($message, 'table or view not found') !== false) {
					return new UnknownTableException($message, 0, $throwable);
				}
				return new DriverQueryException($message, 0, $throwable);
			}
		}
