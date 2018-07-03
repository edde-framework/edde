<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Template\IHelper;
	use Edde\Common\AbstractObject;

	abstract class AbstractHelper extends AbstractObject implements IHelper, ILazyInject {
	}
