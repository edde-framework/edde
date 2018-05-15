<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Service\Collection\EntityManager;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Transaction\Transaction;
	use stdClass;

	class Collection extends Edde implements ICollection {
		use Container;
		use Transaction;
		use Storage;
		use SchemaManager;
		use EntityManager;

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
//			$this->storage->insert(
//				$schema, $source = $this->schemaManager->generate(
//				$schema = $this->getSchema($alias),
//				$source
//			)
//			);
//			return $this->entityManager->entity($schema, $source);
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
