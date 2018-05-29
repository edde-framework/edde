<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use stdClass;

	class Where extends SimpleObject implements IWhere {
		/** @var string */
		protected $name;
		/** @var stdClass */
		protected $where;

		/**
		 * @param string $name
		 */
		public function __construct(string $name) {
			$this->name = $name;
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name;
		}

		/** @inheritdoc */
		public function equalTo(string $alias, string $property, string $param = null): IWhere {
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'param'    => $param ?: $this->name,
			];
			return $this;
		}

		/** @inheritdoc */
		public function toObject(): stdClass {
			return $this->where;
		}
	}
