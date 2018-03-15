<?php
	declare(strict_types=1);
	namespace Edde\Api\Application;

	use Edde\Api\File\IDirectory;

	/**
	 * Formal marker interface for a root directory of an application; all other
	 * directories should be derived from this one.
	 */
	interface IRootDirectory extends IDirectory {
	}
