<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Throwable;

	abstract class AbstractCreateTableQuery extends AbstractQuery {
		use Storage;
		use SchemaManager;
		/** @var string[] */
		protected $types = [];

		/**
		 * @param array $types
		 */
		public function __construct(array $types) {
			$this->types = $types;
		}

		/**
		 * @param string $name
		 *
		 * @throws Throwable
		 */
		public function create(string $name): void {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$table = $schema->getRealName();
				$columns = [];
				$primary = null;
				foreach ($schema->getAttributes() as $attribute) {
					$column = vsprintf('%s %s', [
						$fragment = $this->storage->delimit($attribute->getName()),
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
						$this->storage->delimit(sha1($table . '.primary.' . $primary)),
						$primary,
					]);
				}
				$this->storage->exec(vsprintf("CREATE TABLE %s (\n\t%s\n)", [
					$this->storage->delimit($table),
					implode(",\n\t", $columns),
				]));
			} catch (Throwable $exception) {
				throw $this->storage->exception($exception);
			}
		}

		/**
		 * @param array $names
		 *
		 * @throws Throwable
		 */
		public function creates(array $names): void {
			$this->storage->transaction(function () use ($names) {
				foreach ($names as $name) {
					$this->create($name);
				}
			});
		}

		/**
		 * @param string $type
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		protected function type(string $type): string {
			if (isset($this->types[$type = strtolower($type)])) {
				return $this->types[$type];
			}
			throw new QueryException(sprintf('Unknown type [%s] ', $type, static::class));
		}
	}
