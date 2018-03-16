<?php
	declare(strict_types=1);
	namespace Edde\Service\Runtime;

	use Edde\Api\Runtime\IRuntime;
	use Edde\Exception\Runtime\MissingArgvException;
	use Edde\Object;

	class Runtime extends Object implements IRuntime {
		/**
		 * @var array
		 */
		protected $args;

		/**
		 * @inheritdoc
		 */
		public function isConsoleMode(): bool {
			return PHP_SAPI === 'cli';
		}

		/**
		 * @inheritdoc
		 */
		public function getArguments(): array {
			if ($this->args) {
				return $this->args;
			}
			if (isset($GLOBALS['argv']) === false) {
				throw new MissingArgvException("Variable \$GLOBALS['argv'] is not available!");
			}
			$argv = $GLOBALS['argv'];
			$argumentList = [];
			/**
			 * credit for this method goes here:
			 * https://github.com/pwfisher/CommandLine.php/blob/master/CommandLine.php
			 */
			$key = null;
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
			return $this->args = $argumentList;
		}
	}
