<?php
	declare(strict_types=1);
	namespace Edde\Common\Node;

	use Edde\Node\IAttributes;
	use Edde\Node\INode;

	/**
	 * Default full featured node implementation.
	 */
	class Node extends Tree implements \Edde\Node\INode {
		/** @var string */
		protected $name;
		/** @var mixed */
		protected $value;
		/**  @var \Edde\Node\IAttributes */
		protected $attributes;
		/** @var \Edde\Node\IAttributes */
		protected $metas;
		/** @var INode[] */
		protected $names = [];

		/**
		 * FORTRAN is not a language. It's a way of turning a multi-million dollar mainframe into a $50 programmable scientific calculator.
		 *
		 * @param string     $name
		 * @param array      $attributes
		 * @param mixed|null $value
		 */
		public function __construct(string $name = null, array $attributes = [], $value = null) {
			parent::__construct();
			$this->name = $name;
			$this->value = $value;
			$this->attributes = new Attributes($attributes);
			$this->metas = new Attributes();
		}

		/** @inheritdoc */
		public function setName(?string $name): \Edde\Node\INode {
			$this->name = $name;
			return $this;
		}

		/** @inheritdoc */
		public function getName(): ?string {
			return $this->name;
		}

		/** @inheritdoc */
		public function getAttributes(): IAttributes {
			return $this->attributes;
		}

		/** @inheritdoc */
		public function setAttribute(string $name, $value): \Edde\Node\INode {
			$this->attributes->set($name, $value);
			return $this;
		}

		/** @inheritdoc */
		public function hasAttribute(string $name): bool {
			return $this->attributes->has($name);
		}

		/** @inheritdoc */
		public function getAttribute(string $name, $default = null) {
			return $this->attributes->get($name, $default);
		}

		/** @inheritdoc */
		public function putAttributes(array $attributes): \Edde\Node\INode {
			$this->attributes->put($attributes);
			return $this;
		}

		/** @inheritdoc */
		public function mergeAttributes(array $attributes): \Edde\Node\INode {
			$this->attributes->merge($attributes);
			return $this;
		}

		/** @inheritdoc */
		public function getMetas(): \Edde\Node\IAttributes {
			return $this->metas;
		}

		/** @inheritdoc */
		public function setMeta(string $name, $value): \Edde\Node\INode {
			$this->metas->set($name, $value);
			return $this;
		}

		/** @inheritdoc */
		public function putMetas(array $metaList): \Edde\Node\INode {
			$this->metas->put($metaList);
			return $this;
		}

		/** @inheritdoc */
		public function mergeMetas(array $metas): INode {
			$this->metas->merge($metas);
			return $this;
		}

		/** @inheritdoc */
		public function hasMeta(string $name): bool {
			return $this->metas->has($name);
		}

		/** @inheritdoc */
		public function getMeta(string $name, $default = null) {
			return $this->metas->get($name, $default);
		}

		/** @inheritdoc */
		public function setValue($value): \Edde\Node\INode {
			$this->value = $value;
			return $this;
		}

		/** @inheritdoc */
		public function getValue($default = null) {
			return $this->value !== null ? $this->value : $default;
		}

		/** @inheritdoc */
		public function hasNode(string $name): bool {
			if (isset($this->names[$name])) {
				return true;
			}
			/** @var $node \Edde\Node\INode */
			foreach ($this->trees as $node) {
				if ($node->getName() === $name) {
					return (bool)($this->names[$name] = $node);
				}
			}
			return false;
		}

		/** @inheritdoc */
		public function getNode(string $name): \Edde\Node\INode {
			if (isset($this->names[$name])) {
				return $this->names[$name];
			}
			/** @var $node \Edde\Node\INode */
			foreach ($this->trees as $node) {
				if ($node->getName() === $name) {
					return $this->names[$name] = $node;
				}
			}
			$this->add($this->names[$name] = $node = new Node($name));
			return $node;
		}

		/** @inheritdoc */
		public function __clone() {
			parent::__clone();
			$this->attributes = clone $this->attributes;
			$this->metas = clone $this->metas;
			if (is_object($this->value)) {
				$this->value = clone $this->value;
			}
		}
	}
