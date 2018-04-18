<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Edde;
	use Edde\Http\IResponse;
	use Edde\Service\Bus\RequestService;
	use Edde\Service\Log\LogService;
	use Edde\Service\Router\RouterService;
	use Throwable;
	use function http_response_code;

	class Application extends Edde implements IApplication {
		use RequestService;
		use RouterService;
		use LogService;

		/** @inheritdoc */
		public function run(): int {
			try {
				if ($result = $this->requestService->execute($this->routerService->createRequest())) {
					return $result->getAttribute('code', 0);
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
