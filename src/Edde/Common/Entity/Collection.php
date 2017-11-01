<?php
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\IStream;
		use Edde\Common\Object\Object;

		class Collection extends Object implements ICollection {
			/**
			 * @var IEntityManager
			 */
			protected $entityManager;
			/**
			 * @var IStream
			 */
			protected $stream;
			/**
			 * @var ISchema
			 */
			protected $schema;

			public function __construct(IEntityManager $entityManager, IStream $stream, ISchema $schema) {
				$this->entityManager = $entityManager;
				$this->stream = $stream;
				$this->schema = $schema;
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
					yield $this->entityManager->factory($name, $source);
				}
			}
		}
