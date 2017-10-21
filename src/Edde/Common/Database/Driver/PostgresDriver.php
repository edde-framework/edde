<?php
	namespace Edde\Common\Database\Driver;

		use Edde\Api\Database\Exception\DriverException;
		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Common\Database\AbstractPdoDriver;

		class PostgresDriver extends AbstractPdoDriver {
			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				switch ($type) {
					case 'string':
						return 'CHARACTER VARYING(1024)';
					case 'text':
						return 'TEXT';
					case 'binary':
						return 'BYTEA';
					case 'integer':
						return 'INTEGER';
					case 'float':
						return 'DOUBLE PRECISION';
					case 'bool':
						return 'BOOLEAN';
					case 'datetime':
						return 'TIMESTAMP';
				}
				throw new DriverException(sprintf('Unknown type [%s] for driver [%s]', $type, static::class));
			}

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
				}
				throw new DriverQueryException($message, 0, $throwable);
			}
		}
