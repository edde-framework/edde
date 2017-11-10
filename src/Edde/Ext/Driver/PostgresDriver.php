<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver;

		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\UnknownTableException;

		class PostgresDriver extends AbstractDatabaseDriver {
			/** @inheritdoc */
			protected function getDeleteSql(string $relation): string {
				return 'DELETE FROM ' . $this->delimite($relation) . ' AS r WHERE ';
			}

			/** @inheritdoc */
			public function delimite(string $delimite): string {
				return '"' . str_replace('"', '""', $delimite) . '"';
			}

			/** @inheritdoc */
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
						return 'TIMESTAMP(6)';
				}
				throw new DriverQueryException(sprintf('Unknown type [%s] in driver [%s]', $type, static::class));
			}

			/** @inheritdoc */
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
