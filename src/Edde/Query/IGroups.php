<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IGroups {
		/**
		 * @param string|null $name
		 *
		 * @return IGroup
		 *
		 * @throws QueryException
		 */
		public function group(string $name = null): IGroup;

		/**
		 * @param null|string $name
		 *
		 * @return bool
		 */
		public function hasGroup(?string $name): bool;
	}
