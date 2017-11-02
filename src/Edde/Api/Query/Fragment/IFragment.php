<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IFragment {
			/**
			 * type of this query
			 *
			 * @return string
			 */
			public function getType(): string;
		}
