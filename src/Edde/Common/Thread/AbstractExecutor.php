<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Thread\IExecutor;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractExecutor extends Object implements IExecutor {
		use ConfigurableTrait;
	}
