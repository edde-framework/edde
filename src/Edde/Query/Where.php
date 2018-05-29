<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use stdClass;

	class Where extends SimpleObject implements IWhere {
		/** @var stdClass */
		protected $where;

		/** @inheritdoc */
		public function equalTo(string $alias, string $property, $value): IWhere {
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'value'    => $value,
			];
			return $this;
		}

		/** @inheritdoc */
		public function toObject(): stdClass {
			return $this->where;
		}
	}
