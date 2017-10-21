<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Object\Object;

		abstract class AbstractStorage extends Object implements IStorage {
			public function load(IQuery $query): IEntity {
				foreach ($this->collection($query) as $entity) {
					return $entity;
				}
				throw new EntityNotFoundException('Cannot ');
			}
		}
