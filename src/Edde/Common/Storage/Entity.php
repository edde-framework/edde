<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\EntityException;
		use Edde\Api\Storage\IEntity;
		use Edde\Common\Crate\Crate;

		class Entity extends Crate implements IEntity {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var IEntity[]
			 */
			protected $linkList = [];

			public function __construct(ISchema $schema) {
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function isDirty(): bool {
				if (parent::isDirty()) {
					return true;
				}
				foreach ($this->linkList as $entity) {
					if ($entity->isDirty()) {
						return true;
					}
				}
				return false;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): ICrate {
				$this->linkList = [];
				return parent::commit();
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(): ISchema {
				return $this->schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getPrimaryList(): array {
				$primaryList = [];
				foreach ($this->schema->getPrimaryList() as $property) {
					$primaryList[] = $this->getProperty($property->getName());
				}
				return $primaryList;
			}

			/**
			 * @inheritdoc
			 */
			public function attach(string $name, IEntity $entity): IEntity {
				if ($this->schema->getProperty($name)->isLink() === false) {
					throw new EntityException(sprintf('Cannot attach entity [%s] to entity [%s::%s] - property is not a link.', $entity->getSchema()->getName(), $this->schema->getName(), $name));
				}
				$this->linkList[$name] = $entity;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function hasLinks(): bool {
				return empty($this->linkList) === false;
			}

			/**
			 * @inheritdoc
			 */
			public function getLinkList(): array {
				return $this->linkList;
			}

			public function __clone() {
				parent::__clone();
				$this->linkList = [];
			}
		}
