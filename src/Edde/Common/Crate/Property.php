<?php
	namespace Edde\Common\Crate;

		use Edde\Api\Crate\IProperty;

		class Property implements IProperty {
			/**
			 * @var string
			 */
			protected $name;
			/**
			 * the original value of the property
			 *
			 * @var mixed
			 */
			protected $default;
			/**
			 * current value of property; if it's different from $default, property is dirty
			 *
			 * @var mixed
			 */
			protected $value;
			protected $dirty = false;

			/**
			 * @param string $name
			 */
			public function __construct(string $name) {
				$this->name = $name;
			}

			/**
			 * @inheritdoc
			 */
			public function getName(): string {
				return $this->name;
			}

			/**
			 * @inheritdoc
			 */
			public function setDefault($value): IProperty {
				$this->default = $value;
				$this->value = null;
				$this->dirty = false;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getDefault() {
				return $this->default;
			}

			/**
			 * @inheritdoc
			 */
			public function setValue($value): IProperty {
				$this->dirty = $this->isDiff($this->default, $this->value = $value);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getValue($default = null) {
				return $this->value ?: $default;
			}

			/**
			 * @inheritdoc
			 */
			public function isEmpty(): bool {
				return $this->get() === null;
			}

			/**
			 * @inheritdoc
			 */
			public function get($default = null) {
				return $this->dirty ? $this->value : ($this->default !== null ? $this->default : $default);
			}

			/**
			 * @inheritdoc
			 */
			public function isDirty(): bool {
				return $this->dirty;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IProperty {
				if ($this->dirty === false) {
					return $this;
				}
				$this->default = $this->value;
				$this->value = null;
				$this->dirty = false;
				return $this;
			}

			/**
			 * crate suppose it contains values already for PHP side, thus types in properties
			 * are type in right way; this method supports only PHP scalar types
			 *
			 * @param mixed $current
			 * @param mixed $value
			 *
			 * @return bool
			 */
			protected function isDiff($current, $value): bool {
				switch (gettype($value)) {
					case 'int':
						$current = (int)$current;
						$value = (int)$value;
						return $current !== $value;
					case 'float':
						$current = (float)$current;
						$value = (float)$value;
						return abs($current - $value) > abs(($current - $value) / $value);
					case 'string':
						return (string)$current !== (string)$value;
					case 'boolean':
					case 'bool':
						return $current !== filter_var($value, FILTER_VALIDATE_BOOLEAN);
					default:
						return $current !== $value;
				}
			}
		}
