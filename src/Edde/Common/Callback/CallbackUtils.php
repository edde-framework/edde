<?php
	declare(strict_types=1);

	namespace Edde\Common\Callback;

	use Edde\Common\Object\Object;

	/**
	 * Useful set of methods around callable reflections.
	 */
	class CallbackUtils extends Object {
		/**
		 * safely invoke given callback
		 *
		 * @param callable $function
		 * @param array    $args
		 * @param callable $callback
		 *
		 * @return mixed
		 * @throws \Exception
		 */
		static public function invoke(callable $function, array $args, callable $callback) {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$errorHandler = set_error_handler(function ($severity, $message, $file) use ($callback, &$errorHandler) {
				if ($file === __FILE__ && $callback($message, $severity) !== false) {
					return null;
				} else if ($errorHandler) {
					return call_user_func_array($errorHandler, func_get_args());
				}
				return false;
			});
			try {
				$result = call_user_func_array($function, $args);
				restore_error_handler();
				return $result;
			} catch (\Exception $e) {
				restore_error_handler();
				throw $e;
			}
		}
	}
