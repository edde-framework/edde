<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Query\Fragment\IFragment;

		interface IQuery extends IFragment, IConfigurable {
		}
