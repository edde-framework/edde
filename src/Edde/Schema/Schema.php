<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Object;

	class Schema extends Object implements ISchema {
		/** @var string */
		protected $name;
		/** @var bool */
		protected $relation;
		/** @var string|null */
		protected $alias;
		/** @var IProperty[] */
		protected $properties = [];
		/** @var IProperty */
		protected $primary;
		/** @var IProperty[] */
		protected $uniques = null;
		/** @var ILink[][] */
		protected $linkToList = [];
		/** @var ILink[][] */
		protected $links = [];
		/** @var IRelation[][] */
		protected $relations = [];

		public function __construct(string $name, array $properties, bool $relation, string $alias = null) {
			$this->name = $name;
			$this->properties = $properties;
			$this->relation = $relation;
			$this->alias = $alias;
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name;
		}

		/** @inheritdoc */
		public function hasAlias(): bool {
			return $this->alias !== null;
		}

		/** @inheritdoc */
		public function getAlias(): string {
			return $this->alias;
		}

		/** @inheritdoc */
		public function getRealName(): string {
			return $this->alias ?: $this->name;
		}

		/** @inheritdoc */
		public function isRelation(): bool {
			return $this->relation;
		}

		/** @inheritdoc */
		public function getProperty(string $name): IProperty {
			if (isset($this->properties[$name]) === false) {
				throw new SchemaException(sprintf('Requested unknown property [%s] on schema [%s].', $name, $this->getName()));
			}
			return $this->properties[$name];
		}

		/** @inheritdoc */
		public function getProperties(): array {
			return $this->properties;
		}

		/** @inheritdoc */
		public function hasPrimary(): bool {
			try {
				$this->getPrimary();
				return true;
			} catch (SchemaException $exception) {
				return false;
			}
		}

		/** @inheritdoc */
		public function getPrimary(): IProperty {
			if ($this->primary) {
				return $this->primary;
			}
			foreach ($this->properties as $property) {
				if ($property->isPrimary()) {
					return $this->primary = $property;
				}
			}
			throw new SchemaException(sprintf('Schema [%s] has no primary properties.', $this->getName()));
		}

		/** @inheritdoc */
		public function getUniques(): array {
			if ($this->uniques) {
				return $this->uniques;
			}
			$this->uniques = [];
			foreach ($this->properties as $name => $property) {
				if ($property->isUnique() && $property->isPrimary() === false) {
					$this->uniques[$name] = $property;
				}
			}
			return $this->uniques;
		}

		/** @inheritdoc */
		public function linkTo(ILink $link): ISchema {
			$this->linkToList[null][] = $this->linkToList[$link->getFrom()->getName()][] = $link;
			return $this;
		}

		/** @inheritdoc */
		public function getLinkToList(string $schema = null): array {
			return $this->linkToList[$schema] ?? [];
		}

		/** @inheritdoc */
		public function link(ILink $link): ISchema {
			$this->links[null][] = $this->links[$link->getTo()->getName()][] = $link;
			return $this;
		}

		/** @inheritdoc */
		public function getLinks(string $schema = null): array {
			return $this->links[$schema] ?? [];
		}

		/** @inheritdoc */
		public function hasLink(string $schema): bool {
			return isset($this->links[$schema]) !== false;
		}

		/** @inheritdoc */
		public function getLink(string $schema): ILink {
			if (($count = count($links = $this->getLinks($schema))) === 0) {
				throw new SchemaException(sprintf('There are no links from [%s] to schema [%s].', $this->getName(), $schema));
			} else if ($count !== 1) {
				throw new SchemaException(sprintf('There are more links from [%s] to schema [%s]. You have to specify a link.', $this->getName(), $schema));
			}
			return $links[0];
		}

		/** @inheritdoc */
		public function relation(IRelation $relation): ISchema {
			$schema = $relation->getSchema()->getName();
			$this->relations[null][$schema] = $this->relations[$relation->getTo()->getTo()->getName()][$schema] = $relation;
			return $this;
		}

		/** @inheritdoc */
		public function getRelations(string $schema = null): array {
			return $this->relations[$schema] ?? [];
		}

		/** @inheritdoc */
		public function hasRelation(string $schema): bool {
			return isset($this->relation[$schema]) !== false;
		}

		/** @inheritdoc */
		public function getRelation(string $schema, string $relation = null): IRelation {
			if (($count = count($relations = $this->getRelations($schema))) === 0) {
				throw new SchemaException(sprintf('There are no relations from [%s] to schema [%s].', $this->getName(), $schema));
			} else if ($count !== 1 && $relation === null) {
				throw new SchemaException(sprintf('There are more relations from [%s] to schema [%s]. You have to specify a relation.', $this->getName(), $schema));
			} else if ($count > 1 && $relation && isset($relations[$relation]) === false) {
				throw new SchemaException(sprintf('Requested relation schema [%s] does not exists between [%s] and [%s].', $relation, $this->getName(), $schema));
			} else if ($count > 1 && $relation) {
				return $relations[$relation];
			}
			return reset($relations);
		}
	}
