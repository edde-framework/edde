<?php
	declare(strict_types = 1);

	namespace Edde\Common\Deffered;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Deffered\IDeffered;
	use Edde\Common\AbstractObject;

	/**
	 * Abstract class for all classes supporting "deffered" (lazy) approach.
	 */
	abstract class AbstractDeffered extends AbstractObject implements IDeffered, ILazyInject {
		use DefferedTrait;
	}
