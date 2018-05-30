<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function array_keys;
	use function implode;

	class Chains extends SimpleObject implements IChains {
		/** @var IChain[] */
		protected $chains = [];

		/** @inheritdoc */
		public function chain(string $name = null): IChain {
			if ($this->hasChain($name)) {
				throw new QueryException(sprintf('Chains [%s] is already registered in where list.', $name));
			}
			return $this->chains[$name] = new Chain();
		}

		/** @inheritdoc */
		public function hasChain(?string $name): bool {
			return isset($this->chains[$name]);
		}

		/** @inheritdoc */
		public function getChain(string $name = null): IChain {
			if ($this->hasChain($name) === false) {
				throw new QueryException(sprintf('Requested unknown chain [%s]; available chains [%s].', $name, implode(', ', array_keys($this->chains))));
			}
			return $this->chains[$name];
		}

		/** @inheritdoc */
		public function getChains(): array {
			return $this->chains;
		}

		/** @inheritdoc */
		public function hasChains(): bool {
			return empty($this->chains) === false;
		}
	}
