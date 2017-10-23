<?php
	namespace Edde\Common\Filter;

		use Edde\Api\Filter\Exception\UnknownFilterException;
		use Edde\Api\Filter\IFilter;
		use Edde\Api\Filter\IFilterManager;
		use Edde\Common\Object\Object;

		class FilterManager extends Object implements IFilterManager {
			/**
			 * @var IFilter[]
			 */
			protected $filterList = [];

			/**
			 * @inheritdoc
			 */
			public function registerFilter(string $name, IFilter $filter): IFilterManager {
				$this->filterList[$name] = $filter;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function registerFilterList(array $filterList): IFilterManager {
				foreach ($filterList as $name => $filter) {
					$this->registerFilter($name, $filter);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getFilter(string $name): IFilter {
				if (isset($this->filterList[$name]) === false) {
					throw new UnknownFilterException(sprintf('Requested unknown filter [%s].', $name));
				}
				return $this->filterList[$name];
			}

			/**
			 * @inheritdoc
			 */
			public function filter(array $source): array {
				$result = $source;
				foreach ($source as $k => $v) {
					if (isset($this->filterList[$k])) {
						$result[$k] = $this->filterList[$k]->filter($v);
					}
				}
				return $result;
			}
		}
