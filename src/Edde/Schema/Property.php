<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Collection\IHashMap;
	use Edde\Node\INode;
	use Edde\Object;

	class Property extends Object implements IProperty {
		/** @var IHashMap */
		protected $root;
		/** @var IHashMap */
		protected $hashMap;
		/** @var INode */
		protected $link;

		public function __construct(IHashMap $root, IHashMap $hashMap) {
			$this->root = $root;
			$this->hashMap = $hashMap;
		}

		/** @inheritdoc */
		public function getName(): string {
			return (string)$this->hashMap->get('name');
		}

		/** @inheritdoc */
		public function getType(): string {
			return (string)$this->hashMap->get('type', 'string');
		}

		/** @inheritdoc */
		public function isPrimary(): bool {
			return (bool)$this->hashMap->get('primary', false);
		}

		/** @inheritdoc */
		public function isUnique(): bool {
			return (bool)$this->hashMap->get('unique', false);
		}

		/** @inheritdoc */
		public function isRequired(): bool {
			return (bool)$this->hashMap->get('required', false);
		}

		/** @inheritdoc */
		public function isLink(): bool {
			return (bool)$this->hashMap->get('link', false);
		}

		/** @inheritdoc */
		public function getGenerator(): ?string {
			return $this->hashMap->get('generator');
		}

		/** @inheritdoc */
		public function getFilter(): ?string {
			return $this->hashMap->get('filter');
		}

		/** @inheritdoc */
		public function getSanitizer(): ?string {
			return $this->hashMap->get('sanitizer');
		}

		/** @inheritdoc */
		public function getValidator(): ?string {
			return $this->hashMap->get('validator');
		}

		/** @inheritdoc */
		public function getDefault() {
			return $this->hashMap->get('default');
		}
	}
