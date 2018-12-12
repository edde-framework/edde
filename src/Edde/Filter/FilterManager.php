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
		public function input(array $input, string $schema, string $filter): array {
			$schema = $this->schemaManager->getSchema($schema);
			foreach ($schema->getAttributes() as $name => $attribute) {
				if ($filter = $attribute->getFilter($filter)) {
					$input[$name] = $this->getFilter($filter)->input($input[$name] ?? null);
				}
			}
			return $input;
		}

		/** @inheritdoc */
		public function output(array $input, string $schema, string $filter): array {
			$schema = $this->schemaManager->getSchema($schema);
			foreach ($schema->getAttributes() as $name => $attribute) {
				if ($filter = $attribute->getFilter($filter)) {
					$input[$name] = $this->getFilter($filter)->output($input[$name] ?? null);
				}
			}
			return $input;
		}
	}
