<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Object;

	class Target extends Object implements ITarget {
		/** @var ISchema */
		protected $schema;
		/** @var IProperty */
		protected $property;
		/** @var string */
		protected $name;
		/** @var string */
		protected $realName;
		/** @var string */
		protected $propertyName;

		public function __construct(ISchema $schema, IProperty $property) {
			$this->schema = $schema;
			$this->property = $property;
			$this->name = $schema->getName();
			$this->realName = $schema->getRealName();
			$this->propertyName = $property->getName();
		}

		/** @inheritdoc */
		public function getSchema(): ISchema {
			return $this->schema;
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name;
		}

		/** @inheritdoc */
		public function getRealName(): string {
			return $this->realName;
		}

		/** @inheritdoc */
		public function getProperty(): IProperty {
			return $this->property;
		}

		/** @inheritdoc */
		public function getPropertyName(): string {
			return $this->propertyName;
		}
	}
