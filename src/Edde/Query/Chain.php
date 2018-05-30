<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use stdClass;

	class Chain extends SimpleObject implements IChain {
		/** @var stdClass[] */
		protected $chains = [];

		/** @inheritdoc */
		public function where(string $name): IChain {
			$this->chains = [];
			return $this->and($name);
		}

		/** @inheritdoc */
		public function and(string $name): IChain {
			$this->chains[] = (object)[
				'name'     => $name,
				'operator' => __FUNCTION__,
			];
			return $this;
		}

		/** @inheritdoc */
		public function or(string $name): IChain {
			$this->chains[] = (object)[
				'name'     => $name,
				'operator' => __FUNCTION__,
			];
			return $this;
		}
	}
