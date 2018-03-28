<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Entity\IEntity;
	use Edde\Object;
	use Edde\Schema\ISchema;
	use Edde\Service\Container\Container;
	use Edde\Service\Entity\EntityManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Transaction\Transaction;
	use stdClass;

	class Collection extends Object implements ICollection {
		use Container;
		use Transaction;
		use Storage;
		use SchemaManager;
		use EntityManager;
		/** @var ISchema[] */
		protected $uses = [];

		/** @inheritdoc */
		public function use(string $schema, string $alias = null): ICollection {
			$this->uses[$alias ?: $schema] = $this->schemaManager->load($schema);
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
			$this->transaction->transaction(function () {
				foreach ($this->uses as $schema) {
					$this->storage->create($schema);
				}
			});
			return $this;
		}

		/** @inheritdoc */
		public function insert(string $alias, stdClass $source): IEntity {
			$this->storage->insert(
				$source = $this->schemaManager->generate(
					$schema = $this->getSchema($alias),
					$source
				),
				$schema
			);
			return $this->entityManager->entity($schema, $source);
		}

		/** @inheritdoc */
		public function getSchema(string $alias): ISchema {
			if (isset($this->uses[$alias]) === false) {
				throw new CollectionException(sprintf('Requested alias [%s] is not available in the collection.', $alias));
			}
			return $this->uses[$alias];
		}

		/** @inheritdoc */
		public function getIterator() {
			foreach ($this->storage->collection($this) as $source) {
//				$this->container->create(Record::class, [
//					$this->uses,
//					$source,
//				], __METHOD__);
			}
		}
	}
