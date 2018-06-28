<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Controller\IController;
	use Edde\Edde;
	use Edde\Service\Container\Container;
	use Edde\Service\Router\RouterService;
	use Edde\Service\Utils\StringUtils;
	use function get_class;
	use function is_int;

	class Application extends Edde implements IApplication {
		use Container;
		use RouterService;
		use StringUtils;

		/** @inheritdoc */
		public function run(): int {
			/**
			 * ugly hack to convert input string to form of foo°foo-service which could be later
			 * converted to Foo°FooService and ° could be replaced by "\" leading to Foo\FooService
			 */
			$controller = $this->container->create(
				str_replace(
					'°',
					'\\',
					$this->stringUtils->toCamelCase(
						str_replace(
							['.', '-'],
							['°', '~'],
							($request = $this->routerService->createRequest())->getService()
						)
					)
				),
				[],
				__METHOD__
			);
			if ($controller instanceof IController === false) {
				throw new ApplicationException(sprintf('Requested controller [%s] is not instance of [%s].', get_class($controller), IController::class));
			}
			return is_int($result = $controller->{$this->stringUtils->toCamelHump($request->getMethod())}($request)) ? (int)$result : 0;
		}
	}
