<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function array_keys;
	use function implode;

	class Chain extends SimpleObject implements IChain {
		/** @var IWhere[] */
		protected $wheres;
		/** @var IWhere */
		protected $current;
		protected $chain;

		/**
		 * @param IWhere[] $wheres
		 */
		public function __construct(array $wheres) {
			$this->wheres = $wheres;
		}

		/** @inheritdoc */
		public function select(string $name): IChain {
			if (isset($this->wheres[$name]) === false) {
				throw new QueryException(sprintf("Cannot select where [%s] as it's not available in a chain. Available wheres [%s].", $name, implode(', ', array_keys($this->wheres))));
			}
			$this->current = $this->wheres[$name];
			return $this;
		}

		/** @inheritdoc */
		public function and(string $name): IChain {
			return $this;
		}

		/** @inheritdoc */
		public function or(string $name): IChain {
			return $this;
		}

		/** @inheritdoc */
		public function group(string $name): IChain {
			return $this;
		}
	}
