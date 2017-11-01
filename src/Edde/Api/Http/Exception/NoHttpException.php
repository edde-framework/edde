<?php
	declare(strict_types=1);
	namespace Edde\Api\Http\Exception;

	/**
	 * This exception should be thrown when user wants use http package (http related stuff) in
	 * non-http context (like request service in cli mode).
	 */
		class NoHttpException extends HttpException {
		}
