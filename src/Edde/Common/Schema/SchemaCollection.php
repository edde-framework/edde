<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchemaCollection;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Common\AbstractObject;

	class SchemaCollection extends AbstractObject implements ISchemaCollection {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var ISchemaProperty
		 */
		protected $source;
		/**
		 * @var ISchemaProperty
		 */
		protected $target;

		/**
		 * @param string $name
		 * @param ISchemaProperty $source
		 * @param ISchemaProperty $target
		 */
		public function __construct($name, ISchemaProperty $source, ISchemaProperty $target) {
			$this->name = $name;
			$this->source = $source;
			$this->target = $target;
		}

		public function getName() {
			return $this->name;
		}

		public function getSource() {
			return $this->source;
		}

		public function getTarget() {
			return $this->target;
		}
	}
