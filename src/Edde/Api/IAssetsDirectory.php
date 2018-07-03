<?php
	declare(strict_types = 1);

	namespace Edde\Api;

	use Edde\Api\File\IDirectory;

	/**
	 * Marker interface for a directory where the default edde assets lives.
	 */
	interface IAssetsDirectory extends IDirectory {
	}
