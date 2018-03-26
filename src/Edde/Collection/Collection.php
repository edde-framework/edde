<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Object;
	use Edde\Query\CreateSchemaQuery;
	use Edde\Service\Connection\Connection;
	use Edde\Service\Transaction\Transaction;
	use Throwable;

	class Collection extends Object implements ICollection {
		use Transaction;
		use Connection;
		/** @var string[] */
		protected $uses = [];

		/** @inheritdoc */
		public function use(string $schema, string $alias): ICollection {
			$this->uses[$alias] = $schema;
			return $this;
		}

		/** @inheritdoc */
		public function create(): ICollection {
			try {
				$this->transaction->transaction(function () {
					foreach ($this->uses as $schema) {
						$this->connection->execute(new CreateSchemaQuery($schema));
					}
				});
			} catch (Throwable $exception) {
				throw new CollectionException(sprintf('Collection collection has failed: %s', $exception->getMessage()), 0, $exception);
			}
			return $this;
		}

		/** @inheritdoc */
		public function getIterator() {
			throw new \Exception('not implemented yet');
		}
	}
