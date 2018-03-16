<?php
	declare(strict_types=1);
	namespace Edde\Assets;

	use Edde\Api\File\IDirectory;

	/**
	 * Formal storage of all server-side stuff. Nothing should go outside of this
	 * folder.
	 */
	interface IAssetsDirectory extends IDirectory {
	}
