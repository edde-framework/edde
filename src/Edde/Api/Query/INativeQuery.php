<?php
	namespace Edde\Api\Query;

		use Edde\Api\Config\IConfigurable;

		interface INativeQuery extends IConfigurable {
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
