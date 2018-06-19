<?php
	declare(strict_types=1);
	namespace Edde\Postgres;

	use Edde\Edde;
	use Edde\Query\IQuery;
	use Edde\Query\QueryException;
	use Edde\Schema\ISchema;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Storage\IStorage;
	use Throwable;

	class CreateTableQuery extends Edde implements IQuery {
		use SchemaManager;
		/** @var ISchema */
		protected $schema;

		/**
		 * @param ISchema $schema
		 */
		public function __construct(ISchema $schema) {
			$this->schema = $schema;
		}

		/**
		 * @param IStorage $storage
		 *
		 * @throws Throwable
		 */
		public function create(IStorage $storage): void {
			try {
				$table = $this->schema->getRealName();
				$columns = [];
				$primary = null;
				foreach ($this->schema->getAttributes() as $attribute) {
					$column = vsprintf('%s %s', [
						$fragment = $storage->delimit($attribute->getName()),
						$this->type(
							$attribute->hasSchema() ?
								$this->schemaManager->getSchema($attribute->getSchema())->getPrimary()->getType() :
								$attribute->getType()
						),
					]);
					if ($attribute->isPrimary()) {
						$primary = $fragment;
					} else if ($attribute->isUnique()) {
						$column .= ' UNIQUE';
					}
					if ($attribute->isRequired()) {
						$column .= ' NOT NULL';
					}
					$columns[] = $column;
				}
				if ($primary) {
					$columns[] = vsprintf('CONSTRAINT %s PRIMARY KEY (%s)', [
						$storage->delimit(sha1($table . '.primary.' . $primary)),
						$primary,
					]);
				}
				$storage->exec(vsprintf("CREATE TABLE %s (\n\t%s\n)", [
					$storage->delimit($table),
					implode(",\n\t", $columns),
				]));
			} catch (Throwable $exception) {
				throw $storage->exception($exception);
			}
		}

		/**
		 * @param string $type
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		protected function type(string $type): string {
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
			throw new QueryException(sprintf('Unknown type [%s] ', $type, static::class));
		}
	}
