<?php
	declare(strict_types=1);

	namespace Edde\Common\Log;

	use Edde\Api\Log\ILogDirectory;
	use Edde\Common\File\Directory;

	/**
	 * Default class for log directory.
	 */
	class LogDirectory extends Directory implements ILogDirectory {
	}
