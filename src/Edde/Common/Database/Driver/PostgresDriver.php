<?php
	namespace Edde\Common\Database\Driver;

		use Edde\Api\Database\Exception\DriverException;
		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Common\Database\AbstractDriver;

		class PostgresDriver extends AbstractDriver {
			/**
			 * @inheritdoc
			 */
			public function type(string $type): string {
				switch (strtolower($type)) {
					case 'string':
						return 'CHARACTER VARYING(1024)';
					case 'text':
						return 'TEXT';
					case 'binary':
						return 'BYTEA';
					case 'int':
						return 'INTEGER';
					case 'float':
						return 'DOUBLE PRECISION';
					case 'bool':
						return 'SMALLINT';
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
				} else if (stripos($message, 'duplicate table') !== false) {
					throw new DuplicateTableException($message, 0, $throwable);
				} else if (stripos($message, 'undefined table') !== false) {
					throw new UnknownTableException($message, 0, $throwable);
				}
				throw new DriverQueryException($message, 0, $throwable);
			}
		}
