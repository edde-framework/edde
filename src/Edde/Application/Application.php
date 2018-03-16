<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Inject\Bus\RequestService;
	use Edde\Inject\Log\LogService;
	use Edde\Inject\Router\RouterService;
	use Edde\Object;
	use Throwable;

	class Application extends Object implements IApplication {
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
			} catch (AbortException $exception) {
				if ($previous = $exception->getPrevious()) {
					$this->logService->exception($previous, [
						'edde',
						'exception',
						'application',
					]);
				}
				echo $exception->getMessage();
				/**
				 * when there is an abort exception, it's code is used as primary response
				 */
				return ($code = $exception->getCode()) === 0 ? -1 : $code;
			} catch (Throwable $exception) {
				$this->logService->exception($exception, [
					'edde',
					'exception',
					'application',
				]);
				return ($code = $exception->getCode()) === 0 ? -1 : $code;
			}
		}
	}