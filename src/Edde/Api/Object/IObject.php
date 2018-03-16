<?php
	declare(strict_types=1);
	namespace Edde\Api\Object;

	use Edde\Config\IConfigurable;
	use Edde\Container\IAutowire;

	interface IObject extends IConfigurable, IAutowire {
	}
