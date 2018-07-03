<?php
	declare(strict_types=1);

	namespace Edde\Api\Router\Exception;

	/**
	 * Basically similar to 400 code; this exception should be thrown when
	 * an application (router service) is not able to provide IRequest.
	 */
	class BadRequestException extends RouterServiceException {
	}
