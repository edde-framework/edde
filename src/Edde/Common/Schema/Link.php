<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ITarget;
		use Edde\Common\Object\Object;

		class Link extends Object implements ILink {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var ITarget
			 */
			protected $from;
			/**
			 * @var ITarget
			 */
			protected $to;

			public function __construct(ISchema $schema, ITarget $from, ITarget $to) {
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
			public function getFrom(): ITarget {
				return $this->from;
			}

			/**
			 * @inheritdoc
			 */
			public function getTo(): ITarget {
				return $this->to;
			}
		}
