<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Object\Object;

		abstract class AbstractStorage extends Object implements IStorage {
			/**
			 * @inheritdoc
			 */
			public function load(IQuery $query): IEntity {
				foreach ($this->collection($query) as $entity) {
					return $entity;
				}
				throw new EntityNotFoundException(sprintf('Cannot load any Entity by query [%s].', $query->getDescription()));
			}

			/**
			 * @inheritdoc
			 */
			public function get($primary, string $schema): IEntity {
			}
		}
