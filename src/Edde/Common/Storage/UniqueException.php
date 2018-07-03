<?php
	declare(strict_types=1);

	namespace Edde\Common\Storage;

	use Edde\Api\Storage\StorageException;

	/**
	 * Unique constraint violation.
	 */
	class UniqueException extends StorageException {
	}
