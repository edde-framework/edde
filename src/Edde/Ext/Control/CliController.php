<?php
	declare(strict_types=1);
	namespace Edde\Ext\Control;

		use Edde\Api\Application\Exception\AbortException;
		use Edde\Api\Utils\Inject\StringUtils;

		/**
		 * Control used for a command line content rendering.
		 */
		trait CliController {
			use StringUtils;

			/**
			 * just "nicer" way, how to send an abort exception
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

			/**
			 * @help show all available commands for a cli application
			 */
			public function actionHelp() {
				$this->help();
			}

			public function __call(string $name, array $arguments) {
				printf("Called unknown method on [%s::%s]\n", $this->stringUtils->extract(static::class), strtolower(str_replace('action', '', $name)));
				$this->help();
				printf("Please check your commandline arguments\n");
			}

			protected function help() {
				printf("Available methods on %s:\n", $this->stringUtils->extract(static::class));
				$reflectionClass = new \ReflectionClass($this);
				foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
					if (strpos($name = $reflectionMethod->getName(), 'action') !== false && strlen($name) > 6) {
						printf("- %s:", strtolower(substr($name, 6)));
						if (($match = $this->stringUtils->matchAll($reflectionMethod->getDocComment(), '~@help\s+(?<help>.*)~', true)) !== null) {
							echo "\n\t";
							echo implode("\n\t", $match['help']);
						}
						echo "\n\n";
					}
				}
			}
		}
