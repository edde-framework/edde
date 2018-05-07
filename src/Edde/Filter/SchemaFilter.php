<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Edde;
	use Edde\Schema\ISchema;
	use Edde\Service\Filter\FilterManager;
	use stdClass;
	use function property_exists;

	class SchemaFilter extends Edde implements ISchemaFilter {
		use FilterManager;

		/** @inheritdoc */
		public function input(ISchema $schema, stdClass $stdClass, string $context = null): stdClass {
			$stdClass = clone $stdClass;
			foreach ($schema->getAttributes() as $name => $attribute) {
				/**
				 * if there is a generator and property does not exists, generate a new value; property should not exists to
				 * accept NULL and empty values as generated value
				 */
				if (($generator = $attribute->getFilter('generator')) && (property_exists($stdClass, $name) === false)) {
					$stdClass->$name = $this->filterManager->getFilter(($context ? $context . ':' : '') . $generator)->input(null);
				}
				/**
				 * default value will provide default all the times, thus from this point it's safe to use $stdClass->$name
				 */
				if (property_exists($stdClass, $name) === false) {
					$stdClass->$name = $attribute->getDefault();
				}
				if ($filter = $attribute->getFilter('type')) {
					$stdClass->$name = $this->filterManager->getFilter(($context ? $context . ':' : '') . $filter)->input($stdClass->$name);
				}
				/**
				 * common filter support; filter name is used for both directions
				 */
				if ($filter = $attribute->getFilter('filter')) {
					$stdClass->$name = $this->filterManager->getFilter(($context ? $context . ':' : '') . $filter)->input($stdClass->$name);
				}
			}
			return $stdClass;
		}

		/** @inheritdoc */
		public function output(ISchema $schema, stdClass $stdClass, string $context = null): stdClass {
			$stdClass = clone $stdClass;
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (property_exists($stdClass, $name) === false) {
					$stdClass->$name = $attribute->getDefault();
				}
				if ($filter = $attribute->getFilter('type')) {
					$stdClass->$name = $this->filterManager->getFilter(($context ? $context . ':' : '') . $filter)->output($stdClass->$name);
				}
				if ($filter = $attribute->getFilter('filter')) {
					$stdClass->$name = $this->filterManager->getFilter(($context ? $context . ':' : '') . $filter)->output($stdClass->$name);
				}
			}
			return $stdClass;
		}
	}
