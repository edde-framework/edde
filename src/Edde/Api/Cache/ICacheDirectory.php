<?php
	declare(strict_types = 1);

	namespace Edde\Api\Cache;

	use Edde\Api\File\IDirectory;

	/**
	 * Formal marker interface for a cache directory (if filesystem is used).
	 */
	interface ICacheDirectory extends IDirectory {
	}
