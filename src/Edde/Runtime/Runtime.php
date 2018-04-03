<?php
	declare(strict_types=1);
	namespace Edde\Runtime;

	use Edde\Edde;

	class Runtime extends Edde implements IRuntime {
		/** @var array */
		protected $args;

		/** @inheritdoc */
		public function isConsoleMode(): bool {
			return PHP_SAPI === 'cli';
		}

		/** @inheritdoc */
		public function getArguments(): array {
			if ($this->args) {
				return $this->args;
			}
			if (isset($GLOBALS['argv']) === false) {
				throw new RuntimeException("Variable \$GLOBALS['argv'] is not available!");
			}
			$argv = $GLOBALS['argv'];
			$arguments = [];
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
							$value = $arguments[$key] ?? true;
						}
						$arguments[$key] = $value;
					} else {
						$key = substr($arg, 2, $eqPos - 2);
						$value = substr($arg, $eqPos + 1);
						$arguments[$key] = $value;
					}
				} else if ($arg[0] === '-') {
					if ($arg[2] === '=') {
						$key = $arg[1];
						$value = substr($arg, 3);
						$arguments[$key] = $value;
					} else {
						$chars = str_split(substr($arg, 1));
						foreach ($chars as $char) {
							$key = $char;
							$value = $arguments[$key] ?? true;
							$arguments[$key] = $value;
						}
						if ($i + 1 < $j && $argv[$i + 1][0] !== '-') {
							$arguments[$key] = $argv[$i + 1];
							$i++;
						}
					}
				} else {
					$value = $arg;
					$arguments[] = $value;
				}
			}
			return $this->args = $arguments;
		}
	}
