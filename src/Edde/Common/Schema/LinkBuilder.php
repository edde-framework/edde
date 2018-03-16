<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

	use Edde\Common\Object\Object;

	class LinkBuilder extends Object implements \Edde\Schema\ILinkBuilder {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $sourceSchema;
		/**
		 * @var string
		 */
		protected $sourceProperty;
		/**
		 * @var string
		 */
		protected $targetSchema;
		/**
		 * @var string
		 */
		protected $targetProperty;

		public function __construct(string $name, string $sourceSchema, string $sourceProperty, string $targetSchema, string $targetProperty) {
			$this->name = $name;
			$this->sourceSchema = $sourceSchema;
			$this->sourceProperty = $sourceProperty;
			$this->targetSchema = $targetSchema;
			$this->targetProperty = $targetProperty;
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
		public function getSourceSchema(): string {
			return $this->sourceSchema;
		}

		/**
		 * @inheritdoc
		 */
		public function getSourceProperty(): string {
			return $this->sourceProperty;
		}

		/**
		 * @inheritdoc
		 */
		public function getTargetSchema(): string {
			return $this->targetSchema;
		}

		/**
		 * @inheritdoc
		 */
		public function getTargetProperty(): string {
			return $this->targetProperty;
		}
	}
