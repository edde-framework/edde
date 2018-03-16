<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

	use Edde\Common\Object\Object;
	use Edde\Schema\IProperty;
	use Edde\Schema\ISchema;
	use Edde\Schema\ITarget;

	class Target extends Object implements ITarget {
		/** @var \Edde\Schema\ISchema */
		protected $schema;
		/** @var \Edde\Schema\IProperty */
		protected $property;
		/** @var string */
		protected $name;
		protected $realName;
		/** @var string */
		protected $propertyName;

		public function __construct(\Edde\Schema\ISchema $schema, \Edde\Schema\IProperty $property) {
			$this->schema = $schema;
			$this->property = $property;
			$this->name = $schema->getName();
			$this->realName = $schema->getRealName();
			$this->propertyName = $property->getName();
		}

		/** @inheritdoc */
		public function getSchema(): \Edde\Schema\ISchema {
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
		public function getProperty(): \Edde\Schema\IProperty {
			return $this->property;
		}

		/** @inheritdoc */
		public function getPropertyName(): string {
			return $this->propertyName;
		}
	}
