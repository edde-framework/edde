<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use stdClass;

	class ChainFilter extends AbstractFilter {
		/** @var IFilter[] */
		protected $filters = [];

		/** @inheritdoc */
		public function input($value, ?stdClass $options = null) {
			foreach ($this->filters as $filter) {
				$value = $filter->input($value, $options);
			}
			return $value;
		}

		/** @inheritdoc */
		public function output($value, ?stdClass $options = null) {
			foreach ($this->filters as $filter) {
				$value = $filter->output($value, $options);
			}
			return $value;
		}
	}
