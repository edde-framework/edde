<?php
	declare(strict_types=1);

	namespace Edde\Api\Query;

	/**
	 * Static Query can hold any kind of query and it's parameter; it is usually proprietary
	 * product of {@see IStaticQueryFactory}.
	 */
	interface IStaticQuery {
		/**
		 * query can be arbitrary type
		 *
		 * @return mixed
		 */
		public function getQuery();

		/**
		 * return true, if there are some parameters
		 *
		 * @return bool
		 */
		public function hasParameterList();

		/**
		 * if source IQuery had some parameters, they're accesible here
		 *
		 * @return array
		 */
		public function getParameterList();
	}
