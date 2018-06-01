<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function sha1;

	class Param extends SimpleObject implements IParam {
		/** @var string */
		protected $alias;
		/** @var string */
		protected $property;
		/** @var string */
		protected $name;

		/**
		 * @param string $alias
		 * @param string $property
		 * @param string $name
		 */
		public function __construct(string $alias, string $property, string $name) {
			$this->alias = $alias;
			$this->property = $property;
			$this->name = $name;
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name;
		}

		/** @inheritdoc */
		public function getAlias(): string {
			return $this->alias;
		}

		/** @inheritdoc */
		public function getProperty(): string {
			return $this->property;
		}

		/** @inheritdoc */
		public function getHash(): string {
			return '_' . sha1($this->name);
		}
	}
