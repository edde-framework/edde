<?php
	declare(strict_types=1);
	namespace Edde\Assets;

	use Edde\File\IDirectory;

	/**
	 * Formal marker interface for a root directory of an application; all other
	 * directories should be derived from this one.
	 */
	interface IRootDirectory extends IDirectory {
	}
