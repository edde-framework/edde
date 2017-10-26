<?php
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\UnknownTableException;

		class MysqlDriver extends AbstractDatabaseDriver {
			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				switch (strtolower($type)) {
					case 'string':
						return 'CHARACTER VARYING(1024)';
					case 'text':
						return 'LONGTEXT';
					case 'binary':
						return 'LONGBLOB';
					case 'int':
						return 'INTEGER';
					case 'float':
						return 'DOUBLE PRECISION';
					case 'bool':
						return 'TINYINT';
					case 'datetime':
						return 'DATETIME(6)';
				}
				throw new DriverException(sprintf('Unknown type [%s] for driver [%s]', $type, static::class));
			}

			/**
			 * @inheritdoc
			 */
			protected function exception(\Throwable $throwable) {
				if (stripos($message = $throwable->getMessage(), 'duplicate') !== false) {
					throw new DuplicateEntryException($message, 0, $throwable);
				} else if (stripos($message, 'cannot be null') !== false) {
					throw new NullValueException($message, 0, $throwable);
				} else if (stripos($message, 'table or view already exists') !== false) {
					throw new DuplicateTableException($message, 0, $throwable);
				} else if (stripos($message, 'table or view not found') !== false) {
					throw new UnknownTableException($message, 0, $throwable);
				}
				throw new DriverQueryException($message, 0, $throwable);
			}
		}
