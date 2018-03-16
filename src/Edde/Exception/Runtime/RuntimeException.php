<?php
	declare(strict_types=1);
	namespace Edde\Exception\Runtime;

	use Edde\Exception\EddeException;

	/**
	 * Root exception for Runtime package; this exception should NOT be used
	 * for general runtime faults, just for this package.
	 */
	class RuntimeException extends EddeException {
	}
