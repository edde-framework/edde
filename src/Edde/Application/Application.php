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
	use function http_response_code;

	class Application extends Edde implements IApplication {
		use RouterService;
		use Container;
		use StringUtils;
		use LogService;

		/** @inheritdoc */
		public function run(): int {
			try {
				$request = $this->routerService->createRequest();
				/**
				 * ugly hack to convert input string to form of foo°foo-service which could be later
				 * converted to Foo°FooService and ° could be replaced by "\" leading to Foo\FooService
				 */
				$service = str_replace(
					'°',
					'\\',
					$this->stringUtils->toCamelCase(
						str_replace(['.', '-'], ['°', '~'], $request->getService())
					)
				);
				$method = $this->stringUtils->toCamelHump($request->getMethod());
				if (($response = $this->container->create($service, [], __METHOD__)->{$method}($element)) instanceof IElement) {
					return $response->setReference($element->getUuid());
				}
				return 0;
			} catch (Throwable $exception) {
				$this->logService->exception($exception, [
					'edde',
					'application',
				]);
				http_response_code(
					($code = $exception->getCode()) === 0 ? IResponse::R500_SERVER_ERROR : $code
				);
				return $code === 0 ? -1 : $code;
			}
		}
	}
