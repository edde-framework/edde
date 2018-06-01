<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IBind {
		/**
		 * return param by name and ensures that a value is bound (array key exists)
		 *
		 * @param string $name
		 *
		 * @return IParam
		 *
		 * @throws QueryException
		 */
		public function getParam(string $name): IParam;

		/**
		 * return bound value
		 *
		 * @param string $name
		 *
		 * @return mixed
		 *
		 * @throws QueryException
		 */
		public function getBind(string $name);
	}
