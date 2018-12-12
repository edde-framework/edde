<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Configurable\IConfigurable;
	use Edde\Schema\SchemaException;

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

		/**
		 * filter an array with input filters by a schema; input is meant as an input to PHP side (e.g. converting
		 * string datetime to DateTime object0
		 *
		 * @param array  $input
		 * @param string $schema
		 * @param string $filter
		 *
		 * @return array
		 *
		 * @throws SchemaException
		 * @throws FilterException
		 */
		public function input(array $input, string $schema, string $filter): array;

		/**
		 * filter an array as output (e.g. converting DateTime object to stringified version of date time)
		 *
		 * @param array  $input
		 * @param string $schema
		 * @param string $filter
		 *
		 * @return array
		 *
		 * @throws SchemaException
		 * @throws FilterException
		 */
		public function output(array $input, string $schema, string $filter): array;
	}
