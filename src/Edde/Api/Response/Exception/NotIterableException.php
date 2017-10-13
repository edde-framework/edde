<?php
	namespace Edde\Api\Response\Exception;

		use Edde\Api\Http\Exception\RequestSeviceException;

		/**
		 * Content of a response must be iterable; thus if not, this exception
		 * should be thrown.
		 */
		class NotIterableException extends RequestSeviceException {
		}
