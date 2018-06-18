<?php
	declare(strict_types=1);
	namespace Edde\Mysql;

	use Edde\Hydrator\IHydrator;
	use Edde\Service\Container\Container;
	use Edde\Storage\AbstractPdoStorage;
	use Edde\Storage\DuplicateEntryException;
	use Edde\Storage\DuplicateTableException;
	use Edde\Storage\NullValueException;
	use Edde\Storage\UnknownTableException;
	use Generator;
	use Throwable;

	class MysqlStorage extends AbstractPdoStorage {
		use Container;

		public function __construct(string $config = 'mysql') {
			parent::__construct($config);
		}

		/** @inheritdoc */
		public function hydrate(string $query, IHydrator $hydrator, array $params = []): Generator {
			foreach ($this->exec($query, $params) as $item) {
				yield $hydrator->hydrate($item);
			}
		}

		/** @inheritdoc */
		public function delimit(string $string): string {
			return '`' . str_replace('`', '`' . '`', $string) . '`';
		}

		/** @inheritdoc */
		public function resolveException(Throwable $throwable): Throwable {
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
