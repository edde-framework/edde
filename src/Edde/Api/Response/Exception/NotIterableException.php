<?php
	namespace Edde\Api\Response\Exception;

		/**
		 * Content of a response must be iterable; thus if not, this exception
		 * should be thrown.
		 */
		class NotIterableException extends ExecuteException {
		}
