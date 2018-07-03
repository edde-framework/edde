<?php
	declare(strict_types=1);

	namespace Edde\Common\Filter;

	use Edde\Api\Filter\IFilter;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	/**
	 * Common stuff for a filter implementation.
	 */
	abstract class AbstractFilter extends Object implements IFilter {
		use ConfigurableTrait;
	}
