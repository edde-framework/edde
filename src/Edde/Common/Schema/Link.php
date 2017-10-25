<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\ILink;
		use Edde\Common\Object\Object;

		class Link extends Object implements ILink {
			/**
			 * @var string
			 */
			protected $target;
			/**
			 * @var string|null
			 */
			protected $property;

			public function __construct(string $target, string $property = null) {
				$this->target = $target;
				$this->property = $property;
			}

			/**
			 * @inheritdoc
			 */
			public function getTarget(): string {
				return $this->target;
			}

			/**
			 * @inheritdoc
			 */
			public function getProperty(): ?string {
				return $this->property;
			}
		}
