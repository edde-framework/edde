<?php
	namespace Edde\Api\Filter;

		use Edde\Api\Config\IConfigurable;

		interface IFilterManager extends IConfigurable {
			/**
			 * register the given filter
			 *
			 * @param string  $name
			 * @param IFilter $filter
			 *
			 * @return IFilterManager
			 */
			public function registerFilter(string $name, IFilter $filter): IFilterManager;

			/**
			 * register list of filters
			 *
			 * @param IFilter[] $filterList
			 *
			 * @return IFilterManager
			 */
			public function registerFilterList(array $filterList): IFilterManager;
		}
