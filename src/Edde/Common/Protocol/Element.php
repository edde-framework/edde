<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Node\IAbstractNode;
	use Edde\Api\Node\INode;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;

	class Element extends Node implements IElement {
		/**
		 * @param string      $type
		 * @param string|null $id
		 * @param array|null  $attributeList
		 */
		public function __construct(string $type = null, string $id = null, array $attributeList = []) {
			parent::__construct($type, null, $attributeList);
			$this->setAttribute('id', $id ?: $this->getId());
		}

		/**
		 * @inheritdoc
		 */
		public function getType(): string {
			return $this->getName();
		}

		/**
		 * @inheritdoc
		 */
		public function isType(string $type): bool {
			return $this->getName() === $type;
		}

		/**
		 * @inheritdoc
		 */
		public function setId(string $id): IElement {
			$this->setAttribute('id', $id);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getId(): string {
			if (($id = $this->getAttribute('id')) === null) {
				$this->setAttribute('id', $id = bin2hex(random_bytes(4)) . '-' . implode('-', str_split(bin2hex(random_bytes(8)), 4)) . '-' . bin2hex(random_bytes(6)));
			}
			return $id;
		}

		/**
		 * @inheritdoc
		 */
		public function async(bool $async = true): IElement {
			$this->setAttribute('async', $async);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isAsync(): bool {
			return (bool)$this->getAttribute('async', false);
		}

		/**
		 * @inheritdoc
		 */
		public function setReference(IElement $element): IElement {
			$this->setAttribute('reference', $element->getId());
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasReference(): bool {
			return $this->getAttribute('reference') !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function getReference(): string {
			if ($this->hasReference() === false) {
				throw new ReferenceException(sprintf('Element [%s (%s)] has no reference set.', $this->getType(), static::class));
			}
			return (string)$this->getAttribute('reference');
		}

		/**
		 * @inheritdoc
		 */
		public function data(array $data): IElement {
			$this->putMeta($data);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getData(): array {
			return $this->metaList->array();
		}

		/**
		 * @inheritdoc
		 */
		public function addElement(string $name, IElement $element): IElement {
			if (($node = $this->getElementNode($name)) === null || $node->getName() !== $name) {
				$this->addNode($node = new Element($name));
			}
			$node->addNode($element);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getElementNode(string $name) {
			/** @var $node INode */
			foreach ($this->getNodeList() as $node) {
				if ($node->getName() === $name) {
					return $node;
				}
			}
			return null;
		}

		/**
		 * @inheritdoc
		 */
		public function setElementList(string $name, array $elementList): IElement {
			foreach ($elementList as $element) {
				$this->addElement($name, $element);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getElementList(string $name): array {
			return ($node = $this->getElementNode($name)) ? $node->getNodeList() : [];
		}

		/**
		 * @inheritdoc
		 */
		public function getReferenceBy(string $id): IElement {
			if (empty($elementList = $this->getReferenceList($id)) === false) {
				return reset($elementList);
			}
			throw new ReferenceException(sprintf('Unknown reference id [%s] in Element [%s].', $id, $this->getType()));
		}

		/**
		 * @inheritdoc
		 */
		public function getReferenceList(string $id): array {
			$elementList = [];
			if ($this->hasReference() && $this->getReference() === $id) {
				$elementList[] = $this;
			}
			/** @var $element IElement */
			foreach (NodeIterator::recursive($this) as $element) {
				if ($element->hasReference() && $element->getReference() === $id) {
					$elementList[] = $element;
				}
			}
			return $elementList;
		}

		/**
		 * @inheritdoc
		 */
		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof IElement;
		}
	}
