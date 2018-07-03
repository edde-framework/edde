<?php
	declare(strict_types = 1);

	namespace Edde\Api\Storage;

	/**
	 * This exception should be thrown in situations where data (set) is required, but a query returned
	 * no results (for example object load by query return no result).
	 */
	class EmptyResultException extends StorageException {
	}
