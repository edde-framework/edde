<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\IBoundQuery;
	use Edde\Api\Storage\IRepository;
	use Edde\Api\Storage\LazyStorageTrait;
	use Edde\Common\AbstractObject;
	use Edde\Common\Query\Select\SelectQuery;

	abstract class AbstractRepository extends AbstractObject implements IRepository {
		use LazyContainerTrait;
		use LazyStorageTrait;
		/**
		 * @var ISchema
		 */
		protected $schema;

		public function setSchema(ISchema $schema): IRepository {
			$this->schema = $schema;
			return $this;
		}

		public function store(ICrate $crate): IRepository {
			$this->storage->store($crate);
			return $this;
		}

		public function bound(string $query, ...$parameterList): IBoundQuery {
			return (new BoundQuery())->bind($this->container->create($query, ...$parameterList), $this->storage);
		}

		public function query(): IBoundQuery {
			return $this->bound(SelectQuery::class);
		}
	}
