<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;

	class Binds extends SimpleObject implements IBinds {
		/** @var IBind[] */
		protected $binds;

		/**
		 * @param IBind[] $binds
		 */
		public function __construct(array $binds = []) {
			$this->binds = $binds;
		}

		/** @inheritdoc */
		public function getBinds(): array {
			return $this->binds;
		}
	}
