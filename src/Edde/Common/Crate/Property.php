<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\IProperty;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Common\AbstractObject;

	/**
	 * Crate property implementation.
	 */
	class Property extends AbstractObject implements IProperty {
		/**
		 * property definition of this value
		 *
		 * @var ISchemaProperty
		 */
		protected $schemaProperty;
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
		 * @param ISchemaProperty $schemaProperty
		 * @param mixed|null $value
		 */
		public function __construct(ISchemaProperty $schemaProperty = null, $value = null) {
			$this->schemaProperty = $schemaProperty;
			$this->value = $value;
			$this->dirty = false;
		}

		/**
		 * @inheritdoc
		 * @throws CrateException
		 */
		public function getSchemaProperty(): ISchemaProperty {
			if ($this->schemaProperty === null) {
				throw new CrateException(sprintf('Property [%s] has no schema property definition.', static::class));
			}
			return $this->schemaProperty;
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
				$this->set($value = $default ? (is_callable($default) ? call_user_func($default) : $default) : ($this->schemaProperty->hasGenerator() ? $this->schemaProperty->generator() : null));
				return $this->schemaProperty->getterFilter($this->schemaProperty->filter($value));
			}
			$value = $this->value;
			if ($this->dirty) {
				$value = $this->current;
			}
			return $this->schemaProperty->getterFilter($this->schemaProperty->filter($value));
		}

		/**
		 * @inheritdoc
		 */
		public function set($value): IProperty {
			$value = $this->schemaProperty->setterFilter($this->schemaProperty->filter($value));
			$this->dirty = false;
			$this->current = null;
			if ($this->schemaProperty->isDirty($this->value, $value)) {
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
