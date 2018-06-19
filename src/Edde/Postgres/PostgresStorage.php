<?php
	declare(strict_types=1);
	namespace Edde\Postgres;

	use Edde\Service\Container\Container;
	use Edde\Storage\AbstractPdoStorage;
	use Edde\Storage\DuplicateEntryException;
	use Edde\Storage\DuplicateTableException;
	use Edde\Storage\NullValueException;
	use Edde\Storage\UnknownTableException;
	use Throwable;

	class PostgresStorage extends AbstractPdoStorage {
		use Container;

		public function __construct(string $config = 'postgres') {
			parent::__construct($config);
		}

		/** @inheritdoc */
		public function delimit(string $string): string {
			return '"' . str_replace('"', '"' . '"', $string) . '"';
		}

		/** @inheritdoc */
		public function exception(Throwable $throwable): Throwable {
			if (stripos($message = $throwable->getMessage(), 'unique') !== false) {
				return new DuplicateEntryException($message, 0, $throwable);
			} else if (stripos($message, 'not null') !== false) {
				return new NullValueException($message, 0, $throwable);
			} else if (stripos($message, 'duplicate table') !== false) {
				return new DuplicateTableException($message, 0, $throwable);
			} else if (stripos($message, 'undefined table') !== false) {
				return new UnknownTableException($message, 0, $throwable);
			}
			return $throwable;
		}
	}
