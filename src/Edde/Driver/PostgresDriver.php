<?php
	declare(strict_types=1);
	namespace Edde\Driver;

	use Edde\Exception\Storage\DuplicateEntryException;
	use Edde\Exception\Storage\DuplicateTableException;
	use Edde\Exception\Storage\NullValueException;
	use Edde\Exception\Storage\UnknownTableException;
	use Throwable;

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
			throw new DriverException(sprintf('Unknown type [%s] in driver [%s]', $type, static::class));
		}

		/** @inheritdoc */
		protected function exception(Throwable $throwable): Throwable {
			if (stripos($message = $throwable->getMessage(), 'unique') !== false) {
				return new DuplicateEntryException($message, 0, $throwable);
			} else if (stripos($message, 'not null') !== false) {
				return new NullValueException($message, 0, $throwable);
			} else if (stripos($message, 'duplicate table') !== false) {
				return new DuplicateTableException($message, 0, $throwable);
			} else if (stripos($message, 'undefined table') !== false) {
				return new UnknownTableException($message, 0, $throwable);
			}
			return new DriverException($message, 0, $throwable);
		}
	}
