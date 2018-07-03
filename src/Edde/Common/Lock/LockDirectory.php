<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Lock\ILockDirectory;
	use Edde\Common\File\Directory;

	class LockDirectory extends Directory implements ILockDirectory {
	}
