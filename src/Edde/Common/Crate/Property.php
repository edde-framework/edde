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
				$this->value = $value;
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
				return $this->value ?: $this->default ?: $default;
			}

			/**
			 * @inheritdoc
			 */
			public function isDirty(): bool {
				return $this->default !== $this->value;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IProperty {
				$this->default = $this->value;
				$this->value = false;
				return $this;
			}
		}
