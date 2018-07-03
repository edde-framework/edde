<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISessionDirectory;
	use Edde\Common\File\Directory;

	class SessionDirectory extends Directory implements ISessionDirectory {
	}
