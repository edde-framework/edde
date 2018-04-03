<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Obj3ct;

	class Schema extends Obj3ct implements ISchema {
		/** @var string */
		protected $name;
		/** @var bool */
		protected $relation;
		/** @var string|null */
		protected $alias;
		/** @var IAttribute[] */
		protected $attributes = [];
		/** @var IAttribute */
		protected $primary;
		/** @var IAttribute[] */
		protected $uniques = null;
		/** @var ILink[][] */
		protected $linkToList = [];
		/** @var ILink[][] */
		protected $links = [];
		/** @var IRelation[][] */
		protected $relations = [];

		public function __construct(string $name, array $attributes, bool $relation, string $alias = null) {
			$this->name = $name;
			$this->attributes = $attributes;
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
		public function getAttribute(string $name): IAttribute {
			if (isset($this->attributes[$name]) === false) {
				throw new SchemaException(sprintf('Requested unknown attribute [%s::%s].', $this->getName(), $name));
			}
			return $this->attributes[$name];
		}

		/** @inheritdoc */
		public function getAttributes(): array {
			return $this->attributes;
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
		public function getPrimary(): IAttribute {
			if ($this->primary) {
				return $this->primary;
			}
			foreach ($this->attributes as $attribute) {
				if ($attribute->isPrimary()) {
					return $this->primary = $attribute;
				}
			}
			throw new SchemaException(sprintf('Schema [%s] has no primary attribute.', $this->getName()));
		}

		/** @inheritdoc */
		public function getUniques(): array {
			if ($this->uniques) {
				return $this->uniques;
			}
			$this->uniques = [];
			foreach ($this->attributes as $name => $attribute) {
				if ($attribute->isUnique() && $attribute->isPrimary() === false) {
					$this->uniques[$name] = $attribute;
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
		public function getLinksTo(string $schema = null): array {
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
		public function getRelation(string $schema, string $relation): IRelation {
			if (($count = count($relations = $this->getRelations($schema))) === 0) {
				throw new SchemaException(sprintf('There are no relations from [%s] to schema [%s].', $this->getName(), $schema));
			} else if (isset($relations[$relation]) === false) {
				throw new SchemaException(sprintf('Requested relation schema [%s] does not exists between [%s] and [%s].', $relation, $this->getName(), $schema));
			}
			return $relations[$relation];
		}
	}
