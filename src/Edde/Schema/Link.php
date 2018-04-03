<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Edde;

	class Link extends Edde implements ILink {
		/** @var string */
		protected $name;
		/** @var ITarget */
		protected $from;
		/** @var ITarget */
		protected $to;

		public function __construct(string $name, ITarget $from, ITarget $to) {
			$this->name = $name;
			$this->from = $from;
			$this->to = $to;
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name;
		}

		/** @inheritdoc */
		public function getFrom(): ITarget {
			return $this->from;
		}

		/** @inheritdoc */
		public function getTo(): ITarget {
			return $this->to;
		}
	}
