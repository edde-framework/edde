<?php
	declare(strict_types=1);

	namespace Edde\Common;

	/**
	 * Simple autoloader class.
	 */
	class Autoloader {
		/**
		 * simple autoloader based on namespaces and correct class names
		 *
		 * @param string $namespace
		 * @param string $path
		 * @param bool   $root loader is in the root of autoloaded sources
		 *
		 * @return callable
		 */
		static public function register($namespace, $path, $root = true): callable {
			$namespace .= '\\';
			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$root = $root ? null : $namespace;
			spl_autoload_register($loader = function ($class) use ($namespace, $path, $root) {
				$file = str_replace([
					$namespace,
					'\\',
				], [
					$root,
					'/',
				], $path . '/' . $class . '.php');
				/**
				 * it's strange, but this is performance boost
				 */
				if (file_exists($file) === false) {
					return false;
				}
				/** @noinspection PhpIncludeInspection */
				include_once $file;
				return true;
			}, true, true);
			return $loader;
		}
	}
