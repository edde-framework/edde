<?php
	namespace Edde\Ext\Storage;

		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Storage\AbstractStorage;

		class Neo4jStorage extends AbstractStorage {
			public function execute(IQuery $query) {
			}

			public function query($query, array $parameterList = []) {
			}

			public function native(INativeQuery $nativeQuery) {
			}

			public function start(bool $exclusive = false): IStorage {
				return $this;
			}

			public function commit(): IStorage {
				return $this;
			}

			public function rollback(): IStorage {
				return $this;
			}

			public function save(IEntity $entity): IStorage {
				return $this;
			}

			public function insert(IEntity $entity): IStorage {
				return $this;
			}

			public function push(string $schema, array $source): IEntity {
			}

			public function update(IEntity $entity): IStorage {
				return $this;
			}
		}
