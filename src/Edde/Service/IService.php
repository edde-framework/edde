<?php
	declare(strict_types=1);
	namespace Edde\Service;

	use Edde\Config\IConfigurable;
	use Edde\Container\IAutowire;

	interface IService extends IAutowire, IConfigurable {
	}
