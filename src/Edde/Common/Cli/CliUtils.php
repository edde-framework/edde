<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cli;

	use Edde\Common\AbstractObject;

	/**
	 * Usefull set of command line utilities.
	 */
	class CliUtils extends AbstractObject {
		/**
		 * credit for this method goes here:
		 * https://github.com/pwfisher/CommandLine.php/blob/master/CommandLine.php
		 *
		 * @param array $argv
		 *
		 * @return array
		 */
		static public function getArgumentList(array $argv) {
			$argumentList = [];
			/** @noinspection ForeachInvariantsInspection */
			for ($i = 0, $j = count($argv); $i < $j; $i++) {
				$arg = $argv[$i];
				if (strpos($arg, '--', 0) === 0) {
					$eqPos = strpos($arg, '=');
					if ($eqPos === false) {
						$key = substr($arg, 2);
						if ($i + 1 < $j && $argv[$i + 1][0] !== '-') {
							$value = $argv[$i + 1];
							$i++;
						} else {
							$value = $argumentList[$key] ?? true;
						}
						$argumentList[$key] = $value;
					} else {
						$key = substr($arg, 2, $eqPos - 2);
						$value = substr($arg, $eqPos + 1);
						$argumentList[$key] = $value;
					}
				} else if ($arg[0] === '-') {
					if ($arg[2] === '=') {
						$key = $arg[1];
						$value = substr($arg, 3);
						$argumentList[$key] = $value;
					} else {
						$chars = str_split(substr($arg, 1));
						foreach ($chars as $char) {
							$key = $char;
							$value = $argumentList[$key] ?? true;
							$argumentList[$key] = $value;
						}
						if ($i + 1 < $j && $argv[$i + 1][0] !== '-') {
							$argumentList[$key] = $argv[$i + 1];
							$i++;
						}
					}
				} else {
					$value = $arg;
					$argumentList[] = $value;
				}
			}
			return $argumentList;
		}
	}
