<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Edde;
	use Edde\Service\Schema\SchemaManager;

	class FilterManager extends Edde implements IFilterManager {
		use SchemaManager;
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

		/** @inheritdoc */
		public function input(array $source, string $schema, string $type): array {
			$schema = $this->schemaManager->getSchema($schema);
			foreach ($schema->getAttributes() as $name => $attribute) {
				if ($filter = $attribute->getFilter($type)) {
					$source[$name] = $this->getFilter($filter)->input($source[$name] ?? null);
				}
			}
			foreach (array_keys(array_diff_key($source, $schema->getAttributes())) as $diff) {
				unset($source[$diff]);
			}
			return $source;
		}

		/** @inheritdoc */
		public function output(array $source, string $schema, string $type): array {
			$schema = $this->schemaManager->getSchema($schema);
			foreach ($schema->getAttributes() as $name => $attribute) {
				if ($filter = $attribute->getFilter($type)) {
					$source[$name] = $this->getFilter($filter)->output($source[$name] ?? null);
				}
			}
			foreach (array_keys(array_diff_key($source, $schema->getAttributes())) as $diff) {
				unset($source[$diff]);
			}
			return $source;
		}
	}
