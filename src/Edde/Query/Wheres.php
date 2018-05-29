<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;

	class Wheres extends SimpleObject implements IWheres {
		/** @var IWhere[] */
		protected $wheres = [];

		/** @inheritdoc */
		public function where(string $name, bool $force = false): IWhere {
			if ($force === false && isset($this->wheres[$name])) {
				throw new QueryException(sprintf('Where [%s] is already registered in a where list.', $name));
			}
			/**
			 * override is intentional; it's a bit less transparent, but it allows user to update where
			 * without messing up
			 */
			return $this->wheres[$name] = new Where($name);
		}

		/** @inheritdoc */
		public function isEmpty(): bool {
			return empty($this->wheres) === false;
		}

		/** @inheritdoc */
		public function getWheres(): array {
			return $this->wheres;
		}
	}
