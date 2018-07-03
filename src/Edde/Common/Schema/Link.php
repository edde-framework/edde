<?php
	declare(strict_types=1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ILink;
	use Edde\Api\Schema\IProperty;
	use Edde\Common\Object\Object;

	class Link extends Object implements ILink {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var IProperty
		 */
		protected $source;
		/**
		 * @var IProperty
		 */
		protected $target;

		/**
		 * @param string    $name
		 * @param IProperty $source
		 * @param IProperty $target
		 */
		public function __construct(string $name, IProperty $source, IProperty $target) {
			$this->name = $name;
			$this->source = $source;
			$this->target = $target;
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
		public function getSource(): IProperty {
			return $this->source;
		}

		/**
		 * @inheritdoc
		 */
		public function getTarget(): IProperty {
			return $this->target;
		}
	}
