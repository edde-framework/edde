<?php
	declare(strict_types=1);
	namespace Edde\Api\Runtime\Exception;

	use Edde\Api\EddeException;

	/**
	 * Root exception for Runtime package; this exception should NOT be used
	 * for general runtime faults, just for this package.
	 */
	class RuntimeException extends EddeException {
	}
