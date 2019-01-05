<?php
	declare(strict_types=1);
	namespace Edde\Controller;

	use DateTime;
	use Edde\Service\Utils\StringUtils;
	use ReflectionClass;
	use ReflectionException;
	use function vsprintf;

	/**
	 * Control used for a command line content rendering.
	 */
	abstract class CliController extends AbstractController {
		use StringUtils;

		/**
		 * @help show all available commands for a cli application
		 *
		 * @throws ReflectionException
		 */
		public function actionHelp() {
			$this->help();
		}

		/**
		 * @param string $name
		 * @param array  $arguments
		 *
		 * @throws ReflectionException
		 */
		public function __call(string $name, array $arguments) {
			printf("Called unknown method on [%s::%s]\n", $this->stringUtils->extract(static::class), strtolower(str_replace('action', '', $name)));
			$this->help();
			printf("Please check your commandline arguments\n");
		}

		protected function printf(string $message, ...$params) {
			/** @noinspection PhpUnhandledExceptionInspection */
			printf("[%s] %s\n", (new DateTime())->format('Y-m-d H:i:s'), vsprintf($message, $params));
		}

		/**
		 * @throws ReflectionException
		 */
		protected function help() {
			printf("Available methods on %s:\n", $this->stringUtils->extract(static::class));
			$reflectionClass = new ReflectionClass($this);
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
