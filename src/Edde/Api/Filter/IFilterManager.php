<?php
	namespace Edde\Api\Filter;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Filter\Exception\UnknownFilterException;

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

			/**
			 * @param string $name
			 *
			 * @return IFilter
			 *
			 * @throws UnknownFilterException
			 */
			public function getFilter(string $name): IFilter;

			/**
			 * filter the given input array into output array; all known filters are applied
			 *
			 * @param array $source
			 *
			 * @return array
			 */
			public function filter(array $source): array;
		}
