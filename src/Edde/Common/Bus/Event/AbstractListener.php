<?php
	declare(strict_types=1);
	namespace Edde\Common\Bus\Event;

	use Edde\Api\Bus\Event\IListener;
	use Edde\Object;

	abstract class AbstractListener extends Object implements IListener {
	}
