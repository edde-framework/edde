<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Service\Container\Container;
	use Throwable;

	class PostgresStorage extends AbstractPdoStorage {
		use Container;

		public function __construct(string $config = 'postgres') {
			parent::__construct($config);
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
		public function compiler(): ICompiler {
			return $this->compiler ?: $this->compiler = $this->container->create(PdoCompiler::class, ['"'], __METHOD__);
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
