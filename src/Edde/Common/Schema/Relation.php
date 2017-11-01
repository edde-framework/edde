<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\IRelation;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Object\Object;

		class Relation extends Object implements IRelation {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var ILink
			 */
			protected $sourceLink;
			/**
			 * @var ILink
			 */
			protected $targetLink;

			public function __construct(ISchema $schema, ILink $sourceLink, ILink $targetLink) {
				$this->schema = $schema;
				$this->sourceLink = $sourceLink;
				$this->targetLink = $targetLink;
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(): ISchema {
				return $this->schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getSourceLink(): ILink {
				return $this->sourceLink;
			}

			/**
			 * @inheritdoc
			 */
			public function getTargetLink(): ILink {
				return $this->targetLink;
			}
		}
