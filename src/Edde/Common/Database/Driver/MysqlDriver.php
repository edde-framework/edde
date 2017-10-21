<?php
	namespace Edde\Common\Database\Driver;

		use Edde\Api\Database\Exception\DriverException;
		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Common\Database\AbstractPdoDriver;

		class MysqlDriver extends AbstractPdoDriver {
			public function delimite(string $delimite): string {
				return '`' . str_replace('`', '``', $delimite) . '`';
			}

			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				switch ($type) {
					case 'string':
						return 'CHARACTER VARYING(1024)';
					case 'text':
						return 'LONGTEXT';
					case 'binary':
						return 'LONGBLOB';
					case 'integer':
						return 'INTEGER';
					case 'float':
						return 'DOUBLE PRECISION';
					case 'bool':
						return 'BOOLEAN';
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
				}
				throw new DriverQueryException($message, 0, $throwable);
			}
		}
