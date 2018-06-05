<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Edde;
	use Edde\Http\IResponse;
	use Edde\Service\Container\Container;
	use Edde\Service\Log\LogService;
	use Edde\Service\Router\RouterService;
	use Edde\Service\Utils\StringUtils;
	use Throwable;
	use function get_class;
	use function http_response_code;
	use function is_int;

	class Application extends Edde implements IApplication {
		use Container;
		use RouterService;
		use StringUtils;
		use LogService;

		/** @inheritdoc */
		public function run(): int {
			try {
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
			} catch (Throwable $exception) {
				$this->logService->exception($exception, [
					'edde',
					'application',
				]);
				http_response_code(
					($code = $exception->getCode()) === 0 ? IResponse::R500_SERVER_ERROR : $code
				);
				return $code === 0 ? 1 : $code;
			}
		}
	}
