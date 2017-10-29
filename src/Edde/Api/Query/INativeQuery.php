<?php
	namespace Edde\Api\Query;

		interface INativeQuery {
			/**
			 * return native query (commonly SQL, it could be an array, whatever...)
			 *
			 * @return mixed
			 */
			public function getQuery();

			/**
			 * return an array with parameters for this query
			 *
			 * @return array
			 */
			public function getParameterList(): array;
		}
