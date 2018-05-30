<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;

	class Wheres extends SimpleObject implements IWheres {
		/** @var IWhere[] */
		protected $wheres = [];
		/** @var IChain[] */
		protected $chains = [];

		/** @inheritdoc */
		public function where(string $name): IWhere {
			if ($this->hasWhere($name)) {
				throw new QueryException(sprintf('Where [%s] is already registered in a where list.', $name));
			}
			/**
			 * override is intentional; it's a bit less transparent, but it allows user to update where
			 * without messing up
			 */
			return $this->wheres[$name] = new Where($name);
		}

		/** @inheritdoc */
		public function hasWhere(string $name): bool {
			return isset($this->wheres[$name]);
		}

		/** @inheritdoc */
		public function isEmpty(): bool {
			return empty($this->wheres) === false;
		}

		/** @inheritdoc */
		public function getWheres(): array {
			return $this->wheres;
		}

		/** @inheritdoc */
		public function group(string $name = null): IChain {
			if ($this->hasGroup($name)) {
				throw new QueryException(sprintf('Group [%s] is already registered in where list.', $name));
			}
			return $this->chains[$name] = new Chain();
		}

		/** @inheritdoc */
		public function hasGroup(?string $name): bool {
			return isset($this->chains[$name]);
		}
	}
