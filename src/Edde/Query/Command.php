<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;

	class Command extends SimpleObject implements ICommand {
		/** @var string */
		protected $query;

		/**
		 * @param string $query
		 */
		public function __construct(string $query) {
			$this->query = $query;
		}

		/** @inheritdoc */
		public function getQuery(): string {
			return $this->query;
		}

		/** @inheritdoc */
		public function __toString(): string {
			return $this->getQuery();
		}
	}
