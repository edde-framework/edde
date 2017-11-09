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
			protected $from;
			/**
			 * @var ILink
			 */
			protected $to;

			public function __construct(ISchema $schema, ILink $from, ILink $to) {
				$this->schema = $schema;
				$this->from = $from;
				$this->to = $to;
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
			public function getFrom(): ILink {
				return $this->from;
			}

			/**
			 * @inheritdoc
			 */
			public function getTo(): ILink {
				return $this->to;
			}
		}
