<?php
	namespace Edde\Api\Query\Fragment;

		interface IWhere extends IFragment {
			/**
			 * where equals
			 *
			 * @param string $name
			 *
			 * @return IWhereTo
			 */
			public function eq(string $name): IWhereTo;
		}
