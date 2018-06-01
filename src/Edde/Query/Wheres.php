<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function array_keys;
	use function implode;

	class Wheres extends SimpleObject implements IWheres {
		/** @var IParams */
		protected $params;
		/** @var IWhere[] */
		protected $wheres = [];
		/** @var IChains */
		protected $chains;

		/**
		 * @param IParams $params
		 */
		public function __construct(IParams $params) {
			$this->params = $params;
		}

		/** @inheritdoc */
		public function where(string $name): IWhere {
			if ($this->hasWhere($name)) {
				throw new QueryException(sprintf('Where [%s] is already registered in a where list.', $name));
			}
			/**
			 * override is intentional; it's a bit less transparent, but it allows user to update where
			 * without messing up
			 */
			return $this->wheres[$name] = new Where($name, $this->params);
		}

		/** @inheritdoc */
		public function hasWhere(string $name): bool {
			return isset($this->wheres[$name]);
		}

		/** @inheritdoc */
		public function getWhere(string $name): IWhere {
			if ($this->hasWhere($name) === false) {
				throw new QueryException(sprintf('Requested unknown where [%s]; available wheres [%s].', $name, implode(', ', array_keys($this->wheres))));
			}
			return $this->wheres[$name];
		}

		/** @inheritdoc */
		public function getWheres(): array {
			return $this->wheres;
		}

		/** @inheritdoc */
		public function chains(): IChains {
			return $this->chains ?: $this->chains = new Chains();
		}

		/** @inheritdoc */
		public function getIterator() {
			yield from $this->wheres;
		}
	}
