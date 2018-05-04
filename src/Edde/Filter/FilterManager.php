<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Edde;

	class FilterManager extends Edde implements IFilterManager {
		protected $filters = [];

		/** @inheritdoc */
		public function registerFilter(string $name, IFilter $filter): IFilterManager {
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
		public function getFilter(string $name): IFilter {
			if (isset($this->filters[$name]) === false) {
				throw new FilterException(sprintf('Requested unknown filter [%s].', $name));
			}
			return $this->filters[$name];
		}
	}
