<?php
	declare(strict_types=1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthorizator;
	use Edde\Common\Object;

	/**
	 * Common stuff for an authorizator implementations.
	 */
	abstract class AbstractAuthorizator extends Object implements IAuthorizator {
	}
