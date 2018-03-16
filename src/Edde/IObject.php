<?php
	declare(strict_types=1);
	namespace Edde;

	use Edde\Config\IConfigurable;
	use Edde\Container\IAutowire;

	interface IObject extends IConfigurable, IAutowire {
	}
