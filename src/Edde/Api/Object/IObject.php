<?php
	declare(strict_types=1);

	namespace Edde\Api\Object;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Container\IAutowire;

	interface IObject extends IConfigurable, IAutowire {
	}
