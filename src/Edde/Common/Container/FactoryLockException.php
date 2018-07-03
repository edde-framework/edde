<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\FactoryException;

	/**
	 * Exception thrown when cache is locked (usually circular dependency).
	 */
	class FactoryLockException extends FactoryException {
	}
