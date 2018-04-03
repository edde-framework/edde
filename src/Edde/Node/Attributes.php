<?php
	declare(strict_types=1);
	namespace Edde\Node;

	use Edde\Obj3ct;

	class Attributes extends Obj3ct implements IAttributes {
		protected $attributes;

		public function __construct(array $attributes = null) {
			$this->attributes = $attributes ?? [];
		}

		/** @inheritdoc */
		public function get(string $name, $default = null) {
			return $this->attributes[$name] ?? $default;
		}

		/** @inheritdoc */
		public function hasAttributes(string $name): bool {
			return isset($this->attributes[$name]) && $this->attributes[$name] instanceof IAttributes;
		}

		/** @inheritdoc */
		public function getIterator() {
			yield from $this->attributes;
		}
	}
