<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

	use Edde\Object;
	use Edde\Schema\ILink;
	use Edde\Schema\ISchema;

	class Relation extends Object implements \Edde\Schema\IRelation {
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

		public function __construct(\Edde\Schema\ISchema $schema, ILink $from, ILink $to) {
			$this->schema = $schema;
			$this->from = $from;
			$this->to = $to;
		}

		/**
		 * @inheritdoc
		 */
		public function getSchema(): \Edde\Schema\ISchema {
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
