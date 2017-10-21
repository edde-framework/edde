<?php
	namespace Edde\Common\Crate;

		use Edde\Api\Crate\IProperty;

		class Property implements IProperty {
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
			 * @inheritdoc
			 */
			public function default($value): IProperty {
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
			public function set($value): IProperty {
			}

			/**
			 * @inheritdoc
			 */
			public function get($default = null) {
			}

			/**
			 * @inheritdoc
			 */
			public function isDirty(): bool {
			}
		}
