<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\IStream;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\SelectQuery;

		class Collection extends Object implements ICollection {
			use EntityManager;
			use SchemaManager;
			/**
			 * @var IStream
			 */
			protected $stream;
			/**
			 * @var string
			 */
			protected $schema;

			public function __construct(IStream $stream, string $schema) {
				$this->stream = $stream;
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function query(IQuery $query): ICollection {
				$this->stream->query($query);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): IQuery {
				return $this->stream->getQuery();
			}

			/**
			 * @inheritdoc
			 */
			public function getEntity(): IEntity {
				foreach ($this as $entity) {
					return $entity;
				}
				throw new EntityNotFoundException(sprintf('Cannot load any Entity by query [%s].', $this->stream->getQuery()->getDescription()));
			}

			/**
			 * @inheritdoc
			 */
			public function entity($name): IEntity {
				$this->stream->query($query = new SelectQuery());
				$where = $query->schema($schema = $this->schemaManager->load($this->schema), 'c')->select()->where();
				foreach ($schema->getPrimaryList() as $property) {
					$where->or()->eq($property->getName())->to($name);
				}
				foreach ($schema->getUniqueList() as $property) {
					$where->or()->eq($property->getName())->to($name);
				}
				return $this->getEntity();
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				foreach ($this->stream as $source) {
					yield $this->entityManager->load($this->schema, $source);
				}
			}

			public function __clone() {
				parent::__clone();
				$this->stream = clone $this->stream;
			}
		}
