<?php
	declare(strict_types=1);
	namespace Edde\Api\Http\Exception;

		use Edde\Api\Request\Exception\RequestServiceException;

		class EmptyBodyException extends RequestServiceException {
		}
