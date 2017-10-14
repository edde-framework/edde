<?php
	namespace Edde\Ext\Control;

		use Edde\Api\Application\Exception\AbortException;

		/**
		 * Control used for a command line content rendering.
		 */
		trait CliControl {
			/**
			 * just "nicier" way, how to send an abort exception
			 *
			 * @param string          $message
			 * @param int             $code
			 * @param \Throwable|null $throwable
			 *
			 * @throws AbortException
			 */
			public function abort(string $message, int $code = -1, \Throwable $throwable = null) {
				throw new AbortException($message, $code, $throwable);
			}
		}
