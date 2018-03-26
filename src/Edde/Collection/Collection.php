<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Entity\IEntity;
	use Edde\Object;
	use Edde\Service\Connection\Connection;
	use Edde\Service\Entity\EntityManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Transaction\Transaction;
	use stdClass;
	use Throwable;

	class Collection extends Object implements ICollection {
		use Transaction;
		use Connection;
		use SchemaManager;
		use EntityManager;
		/** @var string[] */
		protected $uses = [];

		/** @inheritdoc */
		public function use(string $schema, string $alias = null): ICollection {
			$this->uses[$alias ?: $schema] = $schema;
			return $this;
		}

		/** @inheritdoc */
		public function uses(array $schemas): ICollection {
			foreach ($schemas as $alias => $schema) {
				$this->use($schema, (string)$alias);
			}
			return $this;
		}

		/** @inheritdoc */
		public function create(): ICollection {
			try {
				$this->transaction->transaction(function () {
					foreach ($this->uses as $schema) {
						$this->connection->create($schema);
					}
				});
			} catch (Throwable $exception) {
				throw new CollectionException(sprintf('Collection collection has failed: %s', $exception->getMessage()), 0, $exception);
			}
			return $this;
		}

		/** @inheritdoc */
		public function save(string $alias, stdClass $source): IEntity {
			try {
				$entity = $this->entityManager->entity($this->getSchema($alias));
				$entity->put();
				return $entity;
			} catch (CollectionException $exception) {
				throw $exception;
			} catch (Throwable $exception) {
				throw new CollectionException(sprintf('Cannot save item to an alias [%s]: %s', $alias, $exception->getMessage()), 0, $exception);
			}
		}

		/** @inheritdoc */
		public function getSchema(string $alias): string {
			if (isset($this->uses[$alias]) === false) {
				throw new CollectionException(sprintf('Requested alias [%s] is not available in the collection.', $alias));
			}
			return $this->uses[$alias];
		}

		/** @inheritdoc */
		public function getIterator() {
			throw new \Exception('not implemented yet');
		}
	}
