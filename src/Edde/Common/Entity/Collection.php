<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\IStream;
		use Edde\Common\Object\Object;

		class Collection extends Object implements ICollection {
			use EntityManager;
			/**
			 * @var IStream
			 */
			protected $stream;
			/**
			 * @var ISchema
			 */
			protected $schema;

			public function __construct(IStream $stream, ISchema $schema) {
				$this->stream = $stream;
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): ISelectQuery {
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
			public function getIterator() {
				$name = $this->schema->getName();
				foreach ($this->stream as $source) {
					yield $this->entityManager->load($name, $source);
				}
			}

			public function __clone() {
				parent::__clone();
				$this->stream = clone $this->stream;
			}
		}
