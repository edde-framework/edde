<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Object;

	class FilterManager extends Object implements IFilterManager {
		/** @var IFilter[] */
		protected $filters = [];

		/** @inheritdoc */
		public function registerFilter(string $name, \Edde\Filter\IFilter $filter): IFilterManager {
			$this->filters[$name] = $filter;
			return $this;
		}

		/** @inheritdoc */
		public function registerFilters(array $filters): IFilterManager {
			foreach ($filters as $name => $filter) {
				$this->registerFilter($name, $filter);
			}
			return $this;
		}

		/** @inheritdoc */
		public function getFilter(string $name): \Edde\Filter\IFilter {
			if (isset($this->filters[$name]) === false) {
				throw new FilterException(sprintf('Requested unknown filter [%s].', $name));
			}
			return $this->filters[$name];
		}

		/**
		 * @inheritdoc
		 *
		 * @throws FilterException
		 */
		public function filter(array $source): array {
			$result = $source;
			foreach ($source as $k => $v) {
				if (isset($this->filters[$k])) {
					$result[$k] = $this->filters[$k]->filter($v);
				}
			}
			return $result;
		}
	}
