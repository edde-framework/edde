<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchemaLink;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Common\AbstractObject;

	class SchemaLink extends AbstractObject implements ISchemaLink {
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
		public function __construct(string $name, ISchemaProperty $source, ISchemaProperty $target) {
			$this->name = $name;
			$this->source = $source;
			$this->target = $target;
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
		public function getSource(): ISchemaProperty {
			return $this->source;
		}

		/**
		 * @inheritdoc
		 */
		public function getTarget(): ISchemaProperty {
			return $this->target;
		}
	}
