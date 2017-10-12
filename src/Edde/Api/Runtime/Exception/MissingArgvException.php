<?php
	declare(strict_types=1);
	namespace Edde\Api\Runtime\Exception;

	/**
	 * Should be thrown when there is an attempt to read $argv when it's not available.
	 */
		class MissingArgvException extends RuntimeException {
		}
