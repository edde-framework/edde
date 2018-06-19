<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Service\Container\Container;
	use Throwable;

	class MysqlStorage extends AbstractPdoStorage {
		use Container;
		const TYPES = [
			'string'   => 'CHARACTER VARYING(1024)',
			'text'     => 'LONGTEXT',
			'binary'   => 'LONGBLOB',
			'int'      => 'INTEGER',
			'float'    => 'DOUBLE PRECISION',
			'bool'     => 'TINYINT',
			'datetime' => 'DATETIME(6)',
		];

		public function __construct(string $config = 'mysql') {
			parent::__construct($config);
		}

		/** @inheritdoc */
		public function delimit(string $string): string {
			return '`' . str_replace('`', '`' . '`', $string) . '`';
		}

		/** @inheritdoc */
		public function exception(Throwable $throwable): Throwable {
			if (stripos($message = $throwable->getMessage(), 'duplicate') !== false) {
				return new DuplicateEntryException($message, 0, $throwable);
			} else if (stripos($message, 'cannot be null') !== false || stripos($message, 'have a default value') !== false) {
				return new NullValueException($message, 0, $throwable);
			} else if (stripos($message, 'table or view already exists') !== false) {
				return new DuplicateTableException($message, 0, $throwable);
			} else if (stripos($message, 'table or view not found') !== false) {
				return new UnknownTableException($message, 0, $throwable);
			}
			return $throwable;
		}
	}
