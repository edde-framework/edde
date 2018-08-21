<?php
	declare(strict_types=1);
	namespace Edde\Application;

	/**
	 * Because router is quite important part of the application,
	 * it has it's own "root" exception.
	 */
	class RouterException extends ApplicationException {
	}
