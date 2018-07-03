<?php
	declare(strict_types=1);

	namespace Edde\Common\File;

	use Edde\Api\File\IRootDirectory;

	/**
	 * Special case of directory used for specifying root directory of something, usually an application.
	 */
	class RootDirectory extends Directory implements IRootDirectory {
	}
