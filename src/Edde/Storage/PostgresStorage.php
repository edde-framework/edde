<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Throwable;

	class PostgresStorage extends AbstractPdoStorage {
		public function __construct(string $config = 'postgres', array $options = []) {
			parent::__construct($config, $options);
		}

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
			throw new StorageException(sprintf('Unknown type [%s] in driver [%s]', $type, static::class));
		}

		/** @inheritdoc */
		protected function exception(Throwable $throwable): Throwable {
			if (stripos($message = $throwable->getMessage(), 'unique') !== false) {
				return new DuplicateEntryException($message, 0, $throwable);
			} else if (stripos($message, 'not null') !== false) {
				return new RequiredValueException($message, 0, $throwable);
			} else if (stripos($message, 'duplicate table') !== false) {
				return new DuplicateTableException($message, 0, $throwable);
			} else if (stripos($message, 'undefined table') !== false) {
				return new UnknownTableException($message, 0, $throwable);
			}
			return $throwable;
		}
	}
