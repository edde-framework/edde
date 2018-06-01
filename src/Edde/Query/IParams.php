<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IParams {
		/**
		 * @param IParam $param
		 *
		 * @return IParams
		 */
		public function param(IParam $param): IParams;

		/**
		 * return params with bound values
		 *
		 * @param array $values
		 *
		 * @return IParam[]
		 *
		 * @throws QueryException
		 */
		public function params(array $values): array;
	}
