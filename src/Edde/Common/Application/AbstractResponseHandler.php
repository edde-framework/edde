<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponseHandler;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractResponseHandler extends Object implements IResponseHandler {
		use ConfigurableTrait;
	}
