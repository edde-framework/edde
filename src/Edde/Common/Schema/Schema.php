<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\Exception\InvalidRelationException;
		use Edde\Api\Schema\Exception\NoPrimaryPropertyException;
		use Edde\Api\Schema\Exception\UnknownPropertyException;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\IProperty;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Object\Object;

		class Schema extends Object implements ISchema {
			/**
			 * @var INode
			 */
			protected $node;
			/**
			 * @var IProperty[]
			 */
			protected $propertyList = [];
			/**
			 * @var IProperty
			 */
			protected $primary;
			/**
			 * @var array
			 */
			protected $uniqueList = null;
			/**
			 * @var ILink[][]
			 */
			protected $linkToList = [];
			/**
			 * @var ILink[][]
			 */
			protected $linkList = [];

			public function __construct(INode $node, array $propertyList) {
				$this->node = $node;
				$this->propertyList = $propertyList;
			}

			/**
			 * @inheritdoc
			 */
			public function getName(): string {
				return $this->node->getAttribute('name');
			}

			/**
			 * @inheritdoc
			 */
			public function hasAlias(): bool {
				return $this->node->getAttribute('alias') !== null;
			}

			/**
			 * @inheritdoc
			 */
			public function getAlias(): string {
				return $this->node->getAttribute('alias');
			}

			/**
			 * @inheritdoc
			 */
			public function getRealName(): string {
				return $this->node->getAttribute('alias', $this->getName());
			}

			/**
			 * @inheritdoc
			 */
			public function isRelation(): bool {
				return (bool)$this->node->getAttribute('is-relation', false);
			}

			/**
			 * @inheritdoc
			 */
			public function getProperty(string $name): IProperty {
				if (isset($this->propertyList[$name]) === false) {
					throw new UnknownPropertyException(sprintf('Requested unknown property [%s] on schema [%s].', $name, $this->getName()));
				}
				return $this->propertyList[$name];
			}

			/**
			 * @inheritdoc
			 */
			public function getPropertyList(): array {
				return $this->propertyList;
			}

			/**
			 * @inheritdoc
			 */
			public function hasPrimary(): bool {
				try {
					$this->getPrimary();
					return true;
				} catch (NoPrimaryPropertyException $exception) {
					return false;
				}
			}

			/**
			 * @inheritdoc
			 */
			public function getPrimary(): IProperty {
				if ($this->primary) {
					return $this->primary;
				}
				foreach ($this->propertyList as $property) {
					if ($property->isPrimary()) {
						return $this->primary = $property;
					}
				}
				throw new NoPrimaryPropertyException(sprintf('Schema [%s] has no primary properties.', $this->getName()));
			}

			/**
			 * @inheritdoc
			 */
			public function getUniqueList(): array {
				if ($this->uniqueList) {
					return $this->uniqueList;
				}
				$this->uniqueList = [];
				foreach ($this->propertyList as $name => $property) {
					if ($property->isUnique() && $property->isPrimary() === false) {
						$this->uniqueList[$name] = $property;
					}
				}
				return $this->uniqueList;
			}

			/**
			 * @inheritdoc
			 */
			public function linkTo(ILink $link): ISchema {
				$this->linkToList[null][] = $this->linkToList[$link->getFrom()->getName()][] = $link;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getLinkToList(string $schema = null): array {
				return $this->linkToList[$schema] ?? [];
			}

			/**
			 * @inheritdoc
			 */
			public function link(ILink $link): ISchema {
				$this->linkList[null][] = $this->linkList[$link->getTo()->getName()][] = $link;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getLinkList(string $schema = null): array {
				return $this->linkList[$schema] ?? [];
			}

			/**
			 * @inheritdoc
			 */
			public function hasLink(string $schema): bool {
				return isset($this->linkList[$schema]) !== false;
			}

			/**
			 * @inheritdoc
			 */
			public function getLink(string $schema): ILink {
				if (($count = count($linkList = $this->getLinkList($schema))) === 0) {
					throw new InvalidRelationException(sprintf('There are no links from [%s] to schema [%s].', $this->getName(), $schema));
				} else if ($count !== 1) {
					throw new InvalidRelationException(sprintf('There are more links from [%s] to schema [%s]. You have to specify a link.', $this->getName(), $schema));
				}
				return $linkList[0];
			}
		}
