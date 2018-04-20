<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Throwable;

	class MysqlStorage extends AbstractPdoStorage {
		/**
		 * @param string $config
		 * @param array  $options
		 */
		public function __construct(string $config = 'mysql', array $options = []) {
			parent::__construct($config, $options);
		}

		/** @inheritdoc */
		public function delimit(string $delimit): string {
			return '`' . str_replace('`', '``', $delimit) . '`';
		}

		/** @inheritdoc */
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
			throw new StorageException(sprintf('Unknown type [%s] in driver [%s]', $type, static::class));
		}

		/** @inheritdoc */
		protected function exception(Throwable $throwable): Throwable {
			if (stripos($message = $throwable->getMessage(), 'duplicate') !== false) {
				return new DuplicateEntryException($message, 0, $throwable);
			} else if (stripos($message, 'cannot be null') !== false || stripos($message, 'have a default value') !== false) {
				return new RequiredValueException($message, 0, $throwable);
			} else if (stripos($message, 'table or view already exists') !== false) {
				return new DuplicateTableException($message, 0, $throwable);
			} else if (stripos($message, 'table or view not found') !== false) {
				return new UnknownTableException($message, 0, $throwable);
			}
			return $throwable;
		}
	}
