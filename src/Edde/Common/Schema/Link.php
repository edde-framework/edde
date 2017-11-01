<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\IProperty;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Object\Object;

		class Link extends Object implements ILink {
			/**
			 * @var ISchema
			 */
			protected $sourceSchema;
			/**
			 * @var ISchema
			 */
			protected $targetSchema;
			/**
			 * @var IProperty
			 */
			protected $sourceProperty;
			/**
			 * @var IProperty
			 */
			protected $targetProperty;

			public function __construct(ISchema $sourceSchema, ISchema $targetSchema, IProperty $sourceProperty, IProperty $targetProperty) {
				$this->sourceSchema = $sourceSchema;
				$this->targetSchema = $targetSchema;
				$this->sourceProperty = $sourceProperty;
				$this->targetProperty = $targetProperty;
			}

			/**
			 * @inheritdoc
			 */
			public function getSourceSchema(): ISchema {
				return $this->sourceSchema;
			}

			/**
			 * @inheritdoc
			 */
			public function getTargetSchema(): ISchema {
				return $this->targetSchema;
			}

			/**
			 * @inheritdoc
			 */
			public function getSourceProperty(): IProperty {
				return $this->sourceProperty;
			}

			/**
			 * @inheritdoc
			 */
			public function getTargetProperty(): IProperty {
				return $this->targetProperty;
			}
		}
