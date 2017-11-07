<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\ITarget;
		use Edde\Common\Object\Object;

		class Link extends Object implements ILink {
			/**
			 * @var string
			 */
			protected $name;
			/**
			 * @var ITarget
			 */
			protected $from;
			/**
			 * @var ITarget
			 */
			protected $to;

			public function __construct(string $name, ITarget $from, ITarget $to) {
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
