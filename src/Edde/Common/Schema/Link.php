<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

	use Edde\Common\Object\Object;
	use Edde\Schema\ITarget;

	class Link extends Object implements \Edde\Schema\ILink {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var \Edde\Schema\ITarget
		 */
		protected $from;
		/**
		 * @var \Edde\Schema\ITarget
		 */
		protected $to;

		public function __construct(string $name, \Edde\Schema\ITarget $from, \Edde\Schema\ITarget $to) {
			$this->name = $name;
			$this->from = $from;
			$this->to = $to;
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
		public function getFrom(): \Edde\Schema\ITarget {
			return $this->from;
		}

		/**
		 * @inheritdoc
		 */
		public function getTo(): \Edde\Schema\ITarget {
			return $this->to;
		}
	}
