<?php
	declare(strict_types = 1);

	namespace Edde\Common\Asset;

	use Edde\Api\Asset\IStorageDirectory;
	use Edde\Common\File\Directory;

	/**
	 * File storage directory; all application generated files should be stored here.
	 */
	class StorageDirectory extends Directory implements IStorageDirectory {
	}
