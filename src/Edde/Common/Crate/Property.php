<?php
	declare(strict_types=1);

	namespace Edde\Common\Crate;

	use Edde\Api\Crate\IProperty;
	use Edde\Api\Schema\IProperty as ISchemaProperty;
	use Edde\Common\Object\Object;

	/**
	 * Crate property implementation.
	 */
	class Property extends Object implements IProperty {
		/**
		 * property definition of this value
		 *
		 * @var ISchemaProperty
		 */
		protected $property;
		/**
		 * the original value of this property
		 *
		 * @var mixed
		 */
		protected $value;
		/**
		 * current value of this property
		 *
		 * @var mixed
		 */
		protected $current;
		/**
		 * has been this property changed?
		 *
		 * @var bool
		 */
		protected $dirty;

		/**
		 * My wife’s cooking is so bad we usually pray after our food.
		 *
		 * @param ISchemaProperty $property
		 * @param mixed|null      $value
		 */
		public function __construct(ISchemaProperty $property = null, $value = null) {
			$this->property = $property;
			$this->value = $value;
			$this->dirty = false;
		}

		/**
		 * @inheritdoc
		 * @throws \Edde\Api\Crate\Exception\CrateException
		 */
		public function getSchemaProperty(): ISchemaProperty {
			if ($this->property === null) {
				throw new \Edde\Api\Crate\Exception\CrateException(sprintf('Property [%s] has no schema property definition.', static::class));
			}
			return $this->property;
		}

		/**
		 * @inheritdoc
		 */
		public function push($value): IProperty {
			$this->dirty = false;
			$this->current = null;
			$this->value = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function get($default = null) {
			if ($this->current === null && $this->value === null) {
				$this->set($value = $default ? (is_callable($default) ? call_user_func($default) : $default) : ($this->property->hasGenerator() ? $this->property->generator() : null));
				return $this->property->getterFilter($this->property->filter($value));
			}
			$value = $this->value;
			if ($this->dirty) {
				$value = $this->current;
			}
			return $this->property->getterFilter($this->property->filter($value));
		}

		/**
		 * @inheritdoc
		 */
		public function set($value): IProperty {
			$value = $this->property->setterFilter($this->property->filter($value));
			$this->dirty = false;
			$this->current = null;
			if ($this->property->isDirty($this->value, $value)) {
				$this->dirty = true;
				$this->current = $value;
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getValue() {
			return $this->value;
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
		public function isEmpty(): bool {
			if ($this->dirty && $this->current === null) {
				return true;
			} else if ($this->dirty === false && $this->value === null) {
				return true;
			}
			return false;
		}

		/**
		 * @inheritdoc
		 */
		public function reset(): IProperty {
			$this->dirty = false;
			$this->current = null;
			return $this;
		}
	}
