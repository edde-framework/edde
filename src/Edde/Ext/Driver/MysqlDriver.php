<?php
	declare(strict_types=1);
	namespace Edde\Ext\Driver;

	use Edde\Exception\Driver\DriverQueryException;
	use Edde\Exception\Storage\DuplicateEntryException;
	use Edde\Exception\Storage\DuplicateTableException;
	use Edde\Exception\Storage\NullValueException;
	use Edde\Exception\Storage\UnknownTableException;
	use Throwable;

	class MysqlDriver extends AbstractDatabaseDriver {
		/** @inheritdoc */
		public function delimite(string $delimite): string {
			return '`' . str_replace('`', '``', $delimite) . '`';
		}

		/**
		 * @inheritdoc
		 *
		 * @throws DriverQueryException
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
			throw new DriverQueryException(sprintf('Unknown type [%s] in driver [%s]', $type, static::class));
		}

		/** @inheritdoc */
		protected function exception(Throwable $throwable): Throwable {
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
