<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Object;

	class Relation extends Object implements IRelation {
		/** @var ISchema */
		protected $schema;
		/** @var ILink */
		protected $from;
		/** @var ILink */
		protected $to;

		public function __construct(ISchema $schema, ILink $from, ILink $to) {
			$this->schema = $schema;
			$this->from = $from;
			$this->to = $to;
		}

		/** @inheritdoc */
		public function getSchema(): ISchema {
			return $this->schema;
		}

		/** @inheritdoc */
		public function getFrom(): ILink {
			return $this->from;
		}

		/** @inheritdoc */
		public function getTo(): ILink {
			return $this->to;
		}
	}
