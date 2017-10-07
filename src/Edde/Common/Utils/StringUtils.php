<?php
	declare(strict_types=1);
	namespace Edde\Common\Utils;

	use Edde\Api\Utils\IStringUtils;
	use Edde\Common\Object\Object;

	class StringUtils extends Object implements IStringUtils {
		/**
		 * @inheritdoc
		 */
		public function match(string $string, string $pattern, bool $named = false, bool $trim = false) {
			$match = null;
			if (($match = preg_match($pattern, $string, $match) ? $match : null) === null) {
				return null;
			}
			if ($named && is_array($match)) {
				foreach ($match as $k => $v) {
					if (is_int($k) || ((is_array($trim) || $trim) && empty($v))) {
						unset($match[$k]);
					}
				}
			}
			if (is_array($trim)) {
				$match = array_merge($trim, $match);
			}
			return $match;
		}
	}
