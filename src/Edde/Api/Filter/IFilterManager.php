<?php
	declare(strict_types=1);
	namespace Edde\Api\Filter;

	use Edde\Api\Config\IConfigurable;
	use Edde\Exception\Filter\UnknownFilterException;

	/**
	 * Filter manager should take care about values coming to PHP side (for example from
	 * database, from request, whatever). All of them should be converted to scalar type.
	 */
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
		 * @param IFilter[] $filters
		 *
		 * @return IFilterManager
		 */
		public function registerFilters(array $filters): IFilterManager;

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
