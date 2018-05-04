<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Config\IConfigurable;

	interface IFilterManager extends IConfigurable {
		/**
		 * register a new filter
		 *
		 * @param string  $name
		 * @param IFilter $filter
		 *
		 * @return IFilterManager
		 */
		public function registerFilter(string $name, IFilter $filter): IFilterManager;

		/**
		 * register array of filters; name => IFilter
		 *
		 * @param IFilter[] $filters
		 *
		 * @return IFilterManager
		 */
		public function registerFilters(array $filters): IFilterManager;

		/**
		 * get a filter or throw an exception
		 *
		 * @param string $name
		 *
		 * @return IFilter
		 *
		 * @throws FilterException
		 */
		public function getFilter(string $name): IFilter;
	}
