<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Router\IRouter;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractRouter extends Object implements IRouter {
		use ConfigurableTrait;
	}
